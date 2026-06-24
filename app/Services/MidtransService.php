<?php

namespace App\Services;

use App\Exceptions\BookingException;
use App\Jobs\ProcessPaymentWebhookJob;
use App\Models\Booking;
use App\Models\BookingPayment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class MidtransService
{
    public function createSnapTransaction(Booking $booking): array
    {
        $serverKey = Config::get('midtrans.server_key');
        $isProduction = Config::get('midtrans.is_production', false);

        $params = [
            'transaction_details' => [
                'order_id' => $booking->booking_code,
                'gross_amount' => (int) $booking->final_price,
            ],
            'customer_details' => [
                'first_name' => $booking->user->name,
                'email' => $booking->user->email,
                'phone' => $booking->user->phone,
            ],
            'item_details' => [
                [
                    'id' => $booking->venue_id,
                    'price' => (int) $booking->final_price,
                    'quantity' => 1,
                    'name' => $booking->venue->name . ' - ' . $booking->booking_date,
                ],
            ],
            'enabled_payments' => ['qris', 'bank_transfer', 'gopay', 'shopeepay'],
        ];

        // If Midtrans SDK is available, use it. Otherwise, return a fake snap token for tests/local.
        if (class_exists('\Midtrans\Snap')) {
            \Midtrans\Config::$serverKey = $serverKey;
            \Midtrans\Config::$isProduction = $isProduction;
            \Midtrans\Config::$isSanitized = Config::get('midtrans.is_sanitized', true);
            \Midtrans\Config::$is3ds = Config::get('midtrans.is_3ds', true);

            $snap = \Midtrans\Snap::createTransaction($params);
            $snapToken = $snap->token ?? null;
            $redirectUrl = $snap->redirect_url ?? null;
        } else {
            // Fallback for environments without Midtrans SDK during tests.
            $snapToken = 'FAKE-SNAP-'.Str::upper(Str::random(8));
            $redirectUrl = null;
            Log::channel('booking')->debug('Midtrans SDK not available, using fake snap token.', ['order_id' => $booking->booking_code]);
        }

        $payment = BookingPayment::query()->updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'payment_method' => 'snap',
                'payment_gateway' => 'midtrans',
                'gateway_order_id' => $booking->booking_code,
                'snap_token' => $snapToken,
                'amount' => $booking->final_price,
                'status' => 'pending',
            ]
        );

        return [
            'snap_token' => $snapToken,
            'redirect_url' => $redirectUrl,
        ];
    }

    public function verifyWebhookSignature(array $payload): bool
    {
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';

        $serverKey = Config::get('midtrans.server_key');

        $computed = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        return hash_equals($computed, (string) $signatureKey);
    }

    public function handleWebhookNotification(array $payload): void
    {
        if (! $this->verifyWebhookSignature($payload)) {
            Log::channel('booking')->warning('Invalid Midtrans webhook signature.', ['payload' => $payload]);
            throw new BookingException('Invalid signature');
        }

        $orderId = $payload['order_id'] ?? null;

        if (! $orderId) {
            Log::channel('booking')->warning('Midtrans webhook missing order_id', ['payload' => $payload]);
            throw new BookingException('Missing order_id');
        }

        $payment = BookingPayment::query()->where('gateway_order_id', $orderId)->first();

        if (! $payment) {
            // If no payment record exists, try to find booking and create a payment record.
            $booking = Booking::query()->where('booking_code', $orderId)->first();
            if (! $booking) {
                Log::channel('booking')->warning('Midtrans webhook: booking not found', ['order_id' => $orderId]);
                throw new BookingException('Booking not found');
            }

            $payment = BookingPayment::query()->create([
                'booking_id' => $booking->id,
                'payment_method' => 'snap',
                'payment_gateway' => 'midtrans',
                'gateway_order_id' => $orderId,
                'snap_token' => $payload['transaction_id'] ?? null,
                'amount' => $payload['gross_amount'] ?? 0,
                'status' => 'pending',
            ]);
        }

        // Idempotency: if already processed as success, skip further processing
        if ($payment->status === 'success') {
            Log::channel('booking')->info('Midtrans webhook received for already-successful payment; skipping.', ['gateway_order_id' => $orderId]);
            return;
        }

        $midtransStatus = $payload['transaction_status'] ?? $payload['transaction_status'] ?? $payload['status'] ?? null;
        $statusCode = $payload['status_code'] ?? null;

        // Map statuses
        $successStates = ['capture', 'settlement'];
        $pendingStates = ['pending'];
        $failedStates = ['deny', 'cancel', 'expire'];

        $newStatus = 'pending';
        if (in_array($midtransStatus, $successStates, true)) {
            $newStatus = 'success';
        } elseif (in_array($midtransStatus, $pendingStates, true)) {
            $newStatus = 'pending';
        } elseif (in_array($midtransStatus, $failedStates, true)) {
            $newStatus = 'failed';
            if ($midtransStatus === 'expire') {
                $newStatus = 'expired';
            }
        }

        $payment->status = $newStatus;
        $payment->gateway_transaction_id = $payload['transaction_id'] ?? $payment->gateway_transaction_id;
        $payment->gateway_response = $payload;
        if ($newStatus === 'success') {
            $payment->paid_at = now();
        }
        $payment->save();

        $booking = $payment->booking()->first();

        if (! $booking) {
            Log::channel('booking')->warning('Payment has no booking relation', ['payment_id' => $payment->id]);
            return;
        }

        if ($newStatus === 'success') {
            $booking->status = 'confirmed';
            $booking->save();

            // Generate QR code (placeholder service)
            if (class_exists(\App\Services\QRCodeService::class)) {
                try {
                    app(\App\Services\QRCodeService::class)->generateQRImage($booking);
                } catch (\Throwable $e) {
                    Log::channel('booking')->error('QR generation failed', ['error' => $e->getMessage()]);
                }
            }

            // Release slot lock
            app(SlotLockService::class)->releaseLock($booking->slot_id, $booking->booking_date->toDateString(), $booking->user_id);

            // Dispatch booking confirmation notification job
            if (class_exists(\App\Jobs\SendBookingConfirmationJob::class)) {
                dispatch(new \App\Jobs\SendBookingConfirmationJob($booking->id));
            }

            if (class_exists(\App\Services\NotificationService::class)) {
                app(\App\Services\NotificationService::class)->scheduleReminders($booking);
            }

            Log::channel('booking')->info('Payment succeeded and booking confirmed.', ['booking_id' => $booking->id, 'payment_id' => $payment->id]);
        } elseif ($newStatus === 'failed' || $newStatus === 'expired') {
            $booking->status = $newStatus;
            $booking->save();

            if (class_exists(\App\Jobs\SendPaymentFailedJob::class)) {
                dispatch(new \App\Jobs\SendPaymentFailedJob($booking->id));
            }

            // Release slot lock
            app(SlotLockService::class)->releaseLock($booking->slot_id, $booking->booking_date->toDateString(), $booking->user_id);

            // Invalidate slot cache if any
            if (method_exists($booking, 'invalidateSlotCache')) {
                $booking->invalidateSlotCache();
            }

            Log::channel('booking')->info('Payment failed/expired and booking updated.', ['booking_id' => $booking->id, 'payment_id' => $payment->id, 'status' => $newStatus]);
        } else {
            // pending
            Log::channel('booking')->info('Payment pending update processed.', ['payment_id' => $payment->id]);
        }
    }
}
