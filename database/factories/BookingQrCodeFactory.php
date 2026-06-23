<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\BookingQrCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookingQrCode>
 */
class BookingQrCodeFactory extends Factory
{
    protected $model = BookingQrCode::class;

    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'token' => $this->faker->sha256,
            'qr_image_path' => null,
            'is_used' => false,
            'scanned_at' => null,
            'scanned_by' => null,
        ];
    }
}
