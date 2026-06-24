<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherUsageFactory extends Factory
{
    protected $model = VoucherUsage::class;

    public function definition(): array
    {
        return [
            'voucher_id' => Voucher::factory(),
            'user_id' => User::factory(),
            'booking_id' => Booking::factory(),
            'discount_amount' => $this->faker->randomFloat(2, 5000, 50000),
            'used_at' => now(),
        ];
    }
}
