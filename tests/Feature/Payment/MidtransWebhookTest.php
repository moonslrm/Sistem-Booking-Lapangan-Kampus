<?php

namespace Tests\Feature\Payment;

use App\Jobs\ProcessPaymentWebhookJob;
use App\Models\Booking;
use App\Models\BookingPayment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MidtransWebhookTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        Config::set('midtrans.server_key', 'secret_test_key');
        Cache::store('array')->flush();
    }

    public function test_webhook_settlement_confirms_booking()
    {
        $booking = Booking::factory()->create(['status' => 'pending_payment']);

        $payment = BookingPayment::factory()->create([
            'booking_id' => $booking->id,
            'payment_gateway' => 'midtrans',
            'gateway_order_id' => $booking->booking_code,
            'status' => 'pending',
        ]);

        $payload = [
            'order_id' => $booking->booking_code,
            'status_code' => '200',
            'gross_amount' => (string) ((int) $payment->amount),
            'signature_key' => '',
            'transaction_status' => 'settlement',
            'transaction_id' => 'trx-'.Str::random(8),
        ];

        $payload['signature_key'] = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].Config::get('midtrans.server_key'));

        $this->postJson('/api/webhook/midtrans', $payload)
            ->assertStatus(200);

        $this->assertDatabaseHas('booking_payments', [
            'id' => $payment->id,
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_webhook_invalid_signature_rejected()
    {
        $booking = Booking::factory()->create(['status' => 'pending_payment']);

        $payload = [
            'order_id' => $booking->booking_code,
            'status_code' => '200',
            'gross_amount' => '100',
            'signature_key' => 'invalidsignature',
            'transaction_status' => 'settlement',
        ];

        $this->postJson('/api/webhook/midtrans', $payload)
            ->assertStatus(403);
    }

    public function test_webhook_duplicate_is_idempotent()
    {
        $booking = Booking::factory()->create(['status' => 'pending_payment']);

        $payment = BookingPayment::factory()->create([
            'booking_id' => $booking->id,
            'payment_gateway' => 'midtrans',
            'gateway_order_id' => $booking->booking_code,
            'status' => 'success',
        ]);

        $payload = [
            'order_id' => $booking->booking_code,
            'status_code' => '200',
            'gross_amount' => (string) ((int) $payment->amount),
            'signature_key' => '',
            'transaction_status' => 'settlement',
            'transaction_id' => 'trx-'.Str::random(8),
        ];

        $payload['signature_key'] = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].Config::get('midtrans.server_key'));

        // Should return 200 and not duplicate processing
        $this->postJson('/api/webhook/midtrans', $payload)
            ->assertStatus(200);

        $this->assertDatabaseHas('booking_payments', [
            'id' => $payment->id,
            'status' => 'success',
        ]);
    }

    public function test_webhook_expire_releases_lock_and_expires_booking()
    {
        $booking = Booking::factory()->create(['status' => 'pending_payment']);

        $payment = BookingPayment::factory()->create([
            'booking_id' => $booking->id,
            'payment_gateway' => 'midtrans',
            'gateway_order_id' => $booking->booking_code,
            'status' => 'pending',
        ]);

        // simulate a lock held
        Cache::add('lock:slot:'.$booking->slot_id.':'.$booking->booking_date->toDateString(), $booking->user_id, 600);
        $this->assertTrue(Cache::has('lock:slot:'.$booking->slot_id.':'.$booking->booking_date->toDateString()));

        $payload = [
            'order_id' => $booking->booking_code,
            'status_code' => '200',
            'gross_amount' => (string) ((int) $payment->amount),
            'signature_key' => '',
            'transaction_status' => 'expire',
            'transaction_id' => 'trx-'.Str::random(8),
        ];

        $payload['signature_key'] = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].Config::get('midtrans.server_key'));

        $this->postJson('/api/webhook/midtrans', $payload)
            ->assertStatus(200);

        $this->assertDatabaseHas('booking_payments', [
            'id' => $payment->id,
            'status' => 'expired',
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'expired',
        ]);

        $this->assertFalse(Cache::has('lock:slot:'.$booking->slot_id.':'.$booking->booking_date->toDateString()));
    }
}
