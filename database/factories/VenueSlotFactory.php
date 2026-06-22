<?php

namespace Database\Factories;

use App\Models\Venue;
use App\Models\VenueSlot;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VenueSlot>
 */
class VenueSlotFactory extends Factory
{
    protected $model = VenueSlot::class;

    public function definition(): array
    {
        $idFaker = FakerFactory::create('id_ID');
        $startHour = $idFaker->numberBetween(7, 20);
        $endHour = min($startHour + $idFaker->numberBetween(1, 2), 22);
        $normal = $idFaker->numberBetween(60000, 200000);

        return [
            'venue_id' => Venue::factory(),
            'day_of_week' => $idFaker->numberBetween(0, 6),
            'start_time' => sprintf('%02d:00:00', $startHour),
            'end_time' => sprintf('%02d:00:00', $endHour),
            'price_normal' => $normal,
            'price_campus' => max($normal - $idFaker->numberBetween(10000, 40000), 10000),
            'is_active' => true,
        ];
    }
}