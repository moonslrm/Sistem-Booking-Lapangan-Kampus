<?php

namespace Tests\Feature\Venue;

use App\Models\Booking;
use App\Models\Venue;
use App\Models\VenueSlot;
use App\Models\VenueClosure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SlotAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['cache.default' => 'array']);
    }

    public function test_empty_slot_is_available(): void
    {
        $venue = Venue::factory()->create();
        $slot = VenueSlot::factory()->create([
            'venue_id' => $venue->id,
            'day_of_week' => now()->dayOfWeek,
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'is_active' => true,
        ]);

        $date = now()->toDateString();

        Cache::flush();

        $response = $this->getJson('/api/v1/venues/'.$venue->id.'/slots?date='.$date);

        $response->assertStatus(200)
            ->assertJsonPath('data.venue_id', $venue->id)
            ->assertJsonPath('data.date', $date)
            ->assertJsonPath('data.slots.0.status', 'available');
    }

    public function test_slot_with_active_booking_is_booked(): void
    {
        $venue = Venue::factory()->create();
        $slot = VenueSlot::factory()->create([
            'venue_id' => $venue->id,
            'day_of_week' => now()->dayOfWeek,
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'is_active' => true,
        ]);

        Booking::factory()->create([
            'venue_id' => $venue->id,
            'slot_id' => $slot->id,
            'booking_date' => now()->toDateString(),
            'start_time' => '11:00:00',
            'end_time' => '13:00:00',
            'status' => 'confirmed',
        ]);

        $date = now()->toDateString();

        Cache::flush();

        $response = $this->getJson('/api/v1/venues/'.$venue->id.'/slots?date='.$date);

        $response->assertStatus(200)
            ->assertJsonPath('data.slots.0.status', 'booked');
    }

    public function test_slot_with_venue_closure_is_maintenance(): void
    {
        $venue = Venue::factory()->create();
        VenueSlot::factory()->create([
            'venue_id' => $venue->id,
            'day_of_week' => now()->dayOfWeek,
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'is_active' => true,
        ]);

        VenueClosure::create([
            'venue_id' => $venue->id,
            'closed_date' => now()->toDateString(),
            'reason' => 'Routine maintenance',
            'closed_by' => null,
        ]);

        Cache::flush();

        $response = $this->getJson('/api/v1/venues/'.$venue->id.'/slots?date='.now()->toDateString());

        $response->assertStatus(200)
            ->assertJsonPath('data.slots.0.status', 'maintenance');
    }

    public function test_cache_is_populated_and_invalidated_after_booking(): void
    {
        $venue = Venue::factory()->create();
        $slot = VenueSlot::factory()->create([
            'venue_id' => $venue->id,
            'day_of_week' => now()->dayOfWeek,
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'is_active' => true,
        ]);

        $date = now()->toDateString();
        $cacheKey = "slot:{$venue->id}:{$date}";

        Cache::flush();

        $this->getJson('/api/v1/venues/'.$venue->id.'/slots?date='.$date)->assertStatus(200);

        $this->assertTrue(Cache::has($cacheKey));

        Booking::factory()->create([
            'venue_id' => $venue->id,
            'slot_id' => $slot->id,
            'booking_date' => $date,
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'status' => 'confirmed',
        ]);

        $this->assertFalse(Cache::has($cacheKey));
    }
}
