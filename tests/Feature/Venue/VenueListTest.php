<?php

namespace Tests\Feature\Venue;

use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VenueListTest extends TestCase
{
    use RefreshDatabase;

    public function test_venues_list_returns_paginated_data(): void
    {
        Venue::factory()->count(20)->create();

        $response = $this->getJson('/api/v1/venues');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => ['pagination' => ['total', 'count', 'per_page', 'current_page', 'total_pages']],
            ]);

        $this->assertCount(15, $response->json('data'));
        $this->assertEquals(20, $response->json('meta.pagination.total'));
    }

    public function test_filter_by_sport_type_returns_matching_venues(): void
    {
        Venue::factory()->count(5)->create(['sport_type' => 'badminton']);
        Venue::factory()->count(3)->create(['sport_type' => 'futsal']);

        $response = $this->getJson('/api/v1/venues?sport_type=futsal');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
        $this->assertEquals('futsal', $response->json('data.0.sport_type'));
    }
}
