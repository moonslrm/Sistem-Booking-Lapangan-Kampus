<?php

namespace Tests\Feature\QRCode;

use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\BookingQrCode;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class QRValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        Storage::fake('public');
    }

    protected function makeConfirmedBooking(): Booking
    {
        $user = User::factory()->create(['role' => 'umum']);
        $user->assignRole('umum');

        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => 'confirmed',
            'booking_date' => now()->toDateString(),
            'start_time' => now()->addMinutes(15)->toTimeString(),
            'end_time' => now()->addHours(2)->toTimeString(),
        ]);

        $payment = BookingPayment::factory()->create([
            'booking_id' => $booking->id,
            'payment_gateway' => 'midtrans',
            'status' => 'success',
        ]);

        return $booking->refresh();
    }

    protected function makeQRCodePayload(Booking $booking): string
    {
        $token = hash_hmac('sha256', json_encode([
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'expires_at' => $booking->booking_date->toDateString() . ' ' . $booking->end_time,
        ]), config('app.key'));

        return json_encode([
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'token' => $token,
        ]);
    }

    public function test_qr_valid_for_confirmed_booking_checkin_success()
    {
        $booking = $this->makeConfirmedBooking();
        $koorlap = User::factory()->create(['role' => 'koorlap']);
        $koorlap->assignRole('koorlap');

        BookingQrCode::factory()->create([
            'booking_id' => $booking->id,
            'token' => hash_hmac('sha256', json_encode([
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'expires_at' => $booking->booking_date->toDateString() . ' ' . $booking->end_time,
            ]), config('app.key')),
            'qr_image_path' => 'qr-codes/' . $booking->id . '.png',
        ]);

        $payload = $this->makeQRCodePayload($booking);

        $response = $this->actingAs($koorlap, 'sanctum')
            ->postJson('/api/v1/qr/validate', ['payload' => $payload]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.booking.status', 'checked_in');
        $this->assertDatabaseHas('booking_qr_codes', [
            'booking_id' => $booking->id,
            'is_used' => true,
        ]);
    }

    public function test_qr_already_used_is_rejected()
    {
        $booking = $this->makeConfirmedBooking();
        $koorlap = User::factory()->create(['role' => 'koorlap']);
        $koorlap->assignRole('koorlap');

        $qrCode = BookingQrCode::factory()->create([
            'booking_id' => $booking->id,
            'token' => hash_hmac('sha256', json_encode([
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'expires_at' => $booking->booking_date->toDateString() . ' ' . $booking->end_time,
            ]), config('app.key')),
            'qr_image_path' => 'qr-codes/' . $booking->id . '.png',
            'is_used' => true,
            'scanned_at' => now()->subMinute(),
        ]);

        $payload = $this->makeQRCodePayload($booking);

        $response = $this->actingAs($koorlap, 'sanctum')
            ->postJson('/api/v1/qr/validate', ['payload' => $payload]);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'QR sudah pernah digunakan pada ' . $qrCode->scanned_at?->toDateTimeString()]);
    }

    public function test_qr_for_cancelled_booking_is_rejected()
    {
        $booking = $this->makeConfirmedBooking();
        $booking->status = 'cancelled';
        $booking->save();

        $koorlap = User::factory()->create(['role' => 'koorlap']);
        $koorlap->assignRole('koorlap');

        BookingQrCode::factory()->create([
            'booking_id' => $booking->id,
            'token' => hash_hmac('sha256', json_encode([
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'expires_at' => $booking->booking_date->toDateString() . ' ' . $booking->end_time,
            ]), config('app.key')),
            'qr_image_path' => 'qr-codes/' . $booking->id . '.png',
        ]);

        $payload = $this->makeQRCodePayload($booking);

        $response = $this->actingAs($koorlap, 'sanctum')
            ->postJson('/api/v1/qr/validate', ['payload' => $payload]);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'Booking tidak aktif (status: cancelled)']);
    }

    public function test_qr_scanned_too_early_is_rejected()
    {
        $booking = $this->makeConfirmedBooking();
        $booking->booking_date = now()->addDays(1)->toDateString();
        $booking->start_time = now()->addHours(24)->toTimeString();
        $booking->end_time = now()->addHours(26)->toTimeString();
        $booking->save();

        $koorlap = User::factory()->create(['role' => 'koorlap']);
        $koorlap->assignRole('koorlap');

        BookingQrCode::factory()->create([
            'booking_id' => $booking->id,
            'token' => hash_hmac('sha256', json_encode([
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'expires_at' => $booking->booking_date->toDateString() . ' ' . $booking->end_time,
            ]), config('app.key')),
            'qr_image_path' => 'qr-codes/' . $booking->id . '.png',
        ]);

        $payload = $this->makeQRCodePayload($booking);

        $response = $this->actingAs($koorlap, 'sanctum')
            ->postJson('/api/v1/qr/validate', ['payload' => $payload]);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'Terlalu awal untuk check-in']);
    }

    public function test_non_koorlap_user_cannot_validate_qr()
    {
        $booking = $this->makeConfirmedBooking();
        $user = User::factory()->create(['role' => 'umum']);
        $user->assignRole('umum');

        $payload = $this->makeQRCodePayload($booking);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/qr/validate', ['payload' => $payload]);

        $response->assertStatus(403);
    }
}
