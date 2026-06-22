<?php

namespace Tests\Feature\Venue;

use App\Models\Review;
use App\Models\Venue;
use App\Models\VenuePhoto;
use App\Models\VenueSlot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VenueDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_venue_detail_includes_slots_and_photos(): void
    {
        $venue = Venue::factory()->create();
        VenueSlot::factory()->count(2)->create(['venue_id' => $venue->id, 'is_active' => true]);
        VenuePhoto::factory()->count(2)->create(['venue_id' => $venue->id]);
        Review::factory()->count(1)->create(['venue_id' => $venue->id]);

        $response = $this->getJson('/api/v1/venues/'.$venue->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $venue->id)
            ->assertJsonPath('data.is_active', true)
            ->assertJsonCount(2, 'data.photos')
            ->assertJsonCount(2, 'data.slots');

        $this->assertIsString($response->json('data.photos.0.url'));
    }

    public function test_inactive_venue_can_be_viewed_with_is_active_false(): void
    {
        $venue = Venue::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/v1/venues/'.$venue->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $venue->id)
            ->assertJsonPath('data.is_active', false);
    }
}
