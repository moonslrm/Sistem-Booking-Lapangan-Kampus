<?php

namespace Database\Factories;

use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $idFaker = FakerFactory::create('id_ID');

        return [
            'name' => $idFaker->name(),
            'email' => $idFaker->unique()->safeEmail(),
            'phone' => $idFaker->phoneNumber(),
            'avatar' => null,
            'role' => $idFaker->randomElement(['waban', 'umum', 'koorlap', 'admin']),
            'is_campus_member' => $idFaker->boolean(40),
            'is_active' => true,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
