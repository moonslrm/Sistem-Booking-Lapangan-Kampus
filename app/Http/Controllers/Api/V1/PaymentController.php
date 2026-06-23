<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessPaymentWebhookJob;
use App\Models\Booking;
use App\Models\BookingPayment;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function initiate($bookingId, Request $request): JsonResponse
    {
        $user = $request->user();
        $booking = Booking::query()->findOrFail($bookingId);

        // ensure the booking belongs to the user
        if ($booking->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $midtrans = app(MidtransService::class);
        $result = $midtrans->createSnapTransaction($booking);

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function status($bookingId): JsonResponse
    {
        $booking = Booking::query()->findOrFail($bookingId);
        $payment = $booking->payment()->first();

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $payment?->status ?? null,
                'paid_at' => $payment?->paid_at?->toDateTimeString() ?? null,
            ],
        ]);
    }

    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->all();

        // Validate signature to avoid processing invalid requests
        $midtrans = app(MidtransService::class);
        if (! ($payload['signature_key'] ?? null) || ! $midtrans->verifyWebhookSignature($payload)) {
            Log::channel('booking')->warning('Midtrans webhook invalid or missing signature', ['payload' => $payload]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Dispatch job for processing and return 200 quickly
        ProcessPaymentWebhookJob::dispatch($payload);

        return response()->json(['message' => 'OK']);
    }
}
