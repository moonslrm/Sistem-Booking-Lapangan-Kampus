<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Venue;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Venue>
 */
class VenueFactory extends Factory
{
    protected $model = Venue::class;

    public function definition(): array
    {
        $idFaker = FakerFactory::create('id_ID');
        $name = $idFaker->randomElement([
            'Lapangan Futsal Garuda',
            'Gelanggang Badminton Nusantara',
            'Arena Basket Merdeka',
            'Lapangan Voli Patriot',
            'Court Tenis Cendekia',
        ]).' '.$idFaker->numberBetween(1, 99);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'sport_type' => $idFaker->randomElement(['futsal', 'badminton', 'basket', 'voli', 'tenis']),
            'description' => $idFaker->sentence(),
            'location' => $idFaker->address(),
            'facilities' => ['parkir', 'toilet', 'kantin'],
            'managed_by' => User::factory(),
            'is_active' => true,
        ];
    }
}