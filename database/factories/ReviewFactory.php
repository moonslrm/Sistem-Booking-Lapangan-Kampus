<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Review;
use App\Models\Venue;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        $idFaker = FakerFactory::create('id_ID');

        return [
            'user_id' => User::factory(),
            'booking_id' => Booking::factory(),
            'venue_id' => Venue::factory(),
            'rating' => $idFaker->numberBetween(3, 5),
            'comment' => $idFaker->sentence(),
            'is_visible' => true,
        ];
    }
}
