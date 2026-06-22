<?php

namespace Database\Factories;

use App\Models\Voucher;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Voucher>
 */
class VoucherFactory extends Factory
{
    protected $model = Voucher::class;

    public function definition(): array
    {
        $idFaker = FakerFactory::create('id_ID');
        $discountType = $idFaker->randomElement(['percentage', 'fixed']);

        return [
            'code' => 'CSBS'.Str::upper(Str::random(6)),
            'name' => 'Promo '.$idFaker->word(),
            'description' => $idFaker->sentence(),
            'discount_type' => $discountType,
            'discount_value' => $discountType === 'percentage'
                ? $idFaker->numberBetween(5, 40)
                : $idFaker->numberBetween(5000, 50000),
            'min_booking_amount' => $idFaker->numberBetween(0, 150000),
            'max_discount_amount' => $idFaker->optional()->numberBetween(15000, 100000),
            'max_total_usage' => $idFaker->randomElement([0, 100, 200]),
            'max_per_user' => $idFaker->numberBetween(1, 3),
            'valid_from' => now()->subDays($idFaker->numberBetween(0, 5))->toDateString(),
            'valid_until' => now()->addDays($idFaker->numberBetween(5, 30))->toDateString(),
            'target_role' => $idFaker->randomElement(['all', 'waban', 'umum']),
            'is_active' => true,
        ];
    }
}