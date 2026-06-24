<?php

namespace Tests\Feature\Voucher;

use App\Models\Booking;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueSlot;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $wabanUser;
    private User $umumUser;
    private Venue $venue;
    private VenueSlot $slot;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'umum']);
        $this->wabanUser = User::factory()->create(['role' => 'waban', 'is_campus_member' => true]);
        $this->umumUser = User::factory()->create(['role' => 'umum']);

        $this->venue = Venue::factory()->create();
        $this->slot = VenueSlot::factory()->create([
            'venue_id' => $this->venue->id,
            'day_of_week' => Carbon::now()->dayOfWeek,
            'is_active' => true,
        ]);
    }

    public function test_percentage_discount_calculated_correctly(): void
    {
        $today = now()->toDateString();
        $voucher = Voucher::factory()->create([
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'min_booking_amount' => 0,
            'max_discount_amount' => null,
            'valid_from' => $today,
            'valid_until' => now()->addMonth()->toDateString(),
            'target_role' => 'all',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/v1/vouchers/validate', [
            'code' => $voucher->code,
            'booking_amount' => 100000,
        ]);

        $response->assertOk()
            ->assertJson(['valid' => true, 'data' => ['discount_amount' => 20000, 'final_amount' => 80000]]);
    }

    public function test_fixed_discount_calculated_correctly(): void
    {
        $today = now()->toDateString();
        $voucher = Voucher::factory()->create([
            'discount_type' => 'fixed',
            'discount_value' => 15000,
            'min_booking_amount' => 0,
            'valid_from' => $today,
            'valid_until' => now()->addMonth()->toDateString(),
            'target_role' => 'all',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/v1/vouchers/validate', [
            'code' => $voucher->code,
            'booking_amount' => 100000,
        ]);

        $response->assertOk()
            ->assertJson(['valid' => true, 'data' => ['discount_amount' => 15000, 'final_amount' => 85000]]);
    }

    public function test_percentage_capped_at_max_discount(): void
    {
        $today = now()->toDateString();
        $voucher = Voucher::factory()->create([
            'discount_type' => 'percentage',
            'discount_value' => 30,
            'min_booking_amount' => 0,
            'max_discount_amount' => 20000,
            'valid_from' => $today,
            'valid_until' => now()->addMonth()->toDateString(),
            'target_role' => 'all',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/v1/vouchers/validate', [
            'code' => $voucher->code,
            'booking_amount' => 100000,
        ]);

        $response->assertOk()
            ->assertJson(['valid' => true, 'data' => ['discount_amount' => 20000, 'final_amount' => 80000]]);
    }

    public function test_expired_voucher_rejected(): void
    {
        $voucher = Voucher::factory()->create([
            'valid_from' => now()->subMonth()->toDateString(),
            'valid_until' => now()->subDay()->toDateString(),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/v1/vouchers/validate', [
            'code' => $voucher->code,
            'booking_amount' => 100000,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['code' => 'Voucher sudah kadaluarsa atau belum berlaku']);
    }

    public function test_not_yet_active_voucher_rejected(): void
    {
        $voucher = Voucher::factory()->create([
            'valid_from' => now()->addDay()->toDateString(),
            'valid_until' => now()->addMonth()->toDateString(),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/v1/vouchers/validate', [
            'code' => $voucher->code,
            'booking_amount' => 100000,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['code' => 'Voucher sudah kadaluarsa atau belum berlaku']);
    }

    public function test_max_total_usage_reached_rejected(): void
    {
        $today = now()->toDateString();
        $voucher = Voucher::factory()->create([
            'max_total_usage' => 2,
            'max_per_user' => 1,
            'valid_from' => $today,
            'valid_until' => now()->addMonth()->toDateString(),
            'is_active' => true,
        ]);

        VoucherUsage::factory()->count(2)->create(['voucher_id' => $voucher->id]);

        $response = $this->actingAs($this->user)->postJson('/api/v1/vouchers/validate', [
            'code' => $voucher->code,
            'booking_amount' => 100000,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['code' => 'Voucher sudah mencapai batas penggunaan']);
    }

    public function test_user_max_per_user_limit_rejected(): void
    {
        $today = now()->toDateString();
        $voucher = Voucher::factory()->create([
            'max_per_user' => 1,
            'valid_from' => $today,
            'valid_until' => now()->addMonth()->toDateString(),
            'is_active' => true,
        ]);

        VoucherUsage::factory()->create([
            'voucher_id' => $voucher->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/v1/vouchers/validate', [
            'code' => $voucher->code,
            'booking_amount' => 100000,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['code' => 'Kamu sudah menggunakan voucher ini']);
    }

    public function test_minimum_booking_amount_not_met_rejected(): void
    {
        $today = now()->toDateString();
        $voucher = Voucher::factory()->create([
            'min_booking_amount' => 100000,
            'valid_from' => $today,
            'valid_until' => now()->addMonth()->toDateString(),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/v1/vouchers/validate', [
            'code' => $voucher->code,
            'booking_amount' => 50000,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['code' => 'Minimum booking Rp 100.000 untuk voucher ini']);
    }

    public function test_role_restriction_rejected(): void
    {
        $today = now()->toDateString();
        $voucher = Voucher::factory()->create([
            'target_role' => 'waban',
            'min_booking_amount' => 0,
            'valid_from' => $today,
            'valid_until' => now()->addMonth()->toDateString(),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->umumUser)->postJson('/api/v1/vouchers/validate', [
            'code' => $voucher->code,
            'booking_amount' => 100000,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['code' => 'Voucher tidak berlaku untuk tipe akunmu']);
    }

    public function test_waban_user_can_use_waban_voucher(): void
    {
        $today = now()->toDateString();
        $voucher = Voucher::factory()->create([
            'target_role' => 'waban',
            'min_booking_amount' => 0,
            'discount_type' => 'fixed',
            'discount_value' => 10000,
            'valid_from' => $today,
            'valid_until' => now()->addMonth()->toDateString(),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->wabanUser)->postJson('/api/v1/vouchers/validate', [
            'code' => $voucher->code,
            'booking_amount' => 100000,
        ]);

        $response->assertOk()
            ->assertJson(['valid' => true, 'data' => ['discount_amount' => 10000, 'final_amount' => 90000]]);
    }

    public function test_voucher_not_found_rejected(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/vouchers/validate', [
            'code' => 'INVALID-CODE',
            'booking_amount' => 100000,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['code' => 'Kode voucher tidak ditemukan']);
    }

    public function test_inactive_voucher_rejected(): void
    {
        $today = now()->toDateString();
        $voucher = Voucher::factory()->create([
            'is_active' => false,
            'valid_from' => $today,
            'valid_until' => now()->addMonth()->toDateString(),
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/v1/vouchers/validate', [
            'code' => $voucher->code,
            'booking_amount' => 100000,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['code' => 'Voucher tidak aktif']);
    }

    public function test_booking_with_voucher_end_to_end(): void
    {
        $user = User::factory()->create(['role' => 'umum']);
        $today = now()->toDateString();
        $voucher = Voucher::factory()->create([
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'min_booking_amount' => 0,
            'valid_from' => $today,
            'valid_until' => now()->addMonth()->toDateString(),
            'target_role' => 'all',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/bookings', [
            'venue_id' => $this->venue->id,
            'slot_id' => $this->slot->id,
            'booking_date' => now()->toDateString(),
            'voucher_code' => $voucher->code,
        ]);

        $response->assertCreated();

        $booking = Booking::first();
        $this->assertNotNull($booking);
        $this->assertEquals($voucher->code, $booking->voucher_code);

        $expectedDiscount = round($booking->total_price * 0.1, 2);
        $this->assertEquals($expectedDiscount, $booking->discount_amount);
        $this->assertEquals($booking->total_price - $expectedDiscount, $booking->final_price);

        $this->assertDatabaseHas('voucher_usages', [
            'voucher_id' => $voucher->id,
            'user_id' => $user->id,
            'booking_id' => $booking->id,
            'discount_amount' => $expectedDiscount,
        ]);
    }

    public function test_active_promos_list_filtered_by_role(): void
    {
        $today = now()->toDateString();

        Voucher::factory()->create(['valid_until' => now()->subDay()->toDateString(), 'is_active' => true]);
        Voucher::factory()->create(['valid_from' => $today, 'valid_until' => now()->addMonth()->toDateString(), 'is_active' => false]);

        $all = Voucher::factory()->create([
            'valid_from' => $today,
            'valid_until' => now()->addMonth()->toDateString(),
            'target_role' => 'all',
            'is_active' => true,
        ]);

        $waban = Voucher::factory()->create([
            'valid_from' => $today,
            'valid_until' => now()->addMonth()->toDateString(),
            'target_role' => 'waban',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->umumUser)->getJson('/api/v1/vouchers/active');
        $response->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.code', $all->code);

        $response = $this->actingAs($this->wabanUser)->getJson('/api/v1/vouchers/active');
        $response->assertOk()->assertJsonCount(2, 'data');
    }
}
