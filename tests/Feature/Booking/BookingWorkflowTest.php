<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueSlot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['cache.default' => 'array']);
    }

    public function test_user_can_create_booking_for_available_slot(): void
    {
        $user = User::factory()->create();
        $venue = Venue::factory()->create(['is_active' => true]);
        $slot = VenueSlot::factory()->create([
            'venue_id' => $venue->id,
            'day_of_week' => now()->dayOfWeek,
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/bookings', [
            'venue_id' => $venue->id,
            'slot_id' => $slot->id,
            'booking_date' => now()->toDateString(),
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'pending_payment')
            ->assertJsonPath('data.venue.id', $venue->id)
            ->assertJsonPath('data.slot.id', $slot->id);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'venue_id' => $venue->id,
            'slot_id' => $slot->id,
            'status' => 'pending_payment',
        ]);
    }

    public function test_booking_creation_fails_when_slot_is_already_booked(): void
    {
        $user = User::factory()->create();
        $venue = Venue::factory()->create(['is_active' => true]);
        $slot = VenueSlot::factory()->create([
            'venue_id' => $venue->id,
            'day_of_week' => now()->dayOfWeek,
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'is_active' => true,
        ]);

        Booking::factory()->create([
            'user_id' => $user->id,
            'venue_id' => $venue->id,
            'slot_id' => $slot->id,
            'booking_date' => now()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/bookings', [
            'venue_id' => $venue->id,
            'slot_id' => $slot->id,
            'booking_date' => now()->toDateString(),
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'data' => null,
            ]);
    }

    public function test_user_can_cancel_confirmed_booking_before_deadline(): void
    {
        $user = User::factory()->create();
        $venue = Venue::factory()->create(['is_active' => true]);
        $slot = VenueSlot::factory()->create([
            'venue_id' => $venue->id,
            'day_of_week' => now()->addDays(2)->dayOfWeek,
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'is_active' => true,
        ]);

        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'venue_id' => $venue->id,
            'slot_id' => $slot->id,
            'booking_date' => now()->addDays(2)->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user, 'sanctum')->patchJson('/api/v1/bookings/'.$booking->id.'/cancel', [
            'reason' => 'Schedule changed',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'cancelled');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled',
        ]);
    }
}
