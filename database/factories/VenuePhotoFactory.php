<?php

namespace Database\Factories;

use App\Models\Venue;
use App\Models\VenuePhoto;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VenuePhoto>
 */
class VenuePhotoFactory extends Factory
{
    protected $model = VenuePhoto::class;

    public function definition(): array
    {
        $idFaker = FakerFactory::create('id_ID');

        return [
            'venue_id' => Venue::factory(),
            'photo_path' => 'venues/'.($idFaker->numberBetween(1, 9999)).'/'.uniqid('photo_').'.jpg',
            'sort_order' => 1,
        ];
    }
}
