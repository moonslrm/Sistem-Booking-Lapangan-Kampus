<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueSlot;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $idFaker = FakerFactory::create('id_ID');
        $duration = $idFaker->randomElement([1, 1.5, 2]);
        $pricePerHour = $idFaker->numberBetween(70000, 180000);
        $total = $pricePerHour * $duration;
        $discount = $idFaker->randomElement([0, 5000, 10000]);

        return [
            'user_id' => User::factory(),
            'venue_id' => Venue::factory(),
            'slot_id' => VenueSlot::factory(),
            'booking_date' => now()->addDays($idFaker->numberBetween(1, 14))->toDateString(),
            'start_time' => '19:00:00',
            'end_time' => '21:00:00',
            'duration_hours' => $duration,
            'price_per_hour' => $pricePerHour,
            'total_price' => $total,
            'discount_amount' => $discount,
            'final_price' => max($total - $discount, 0),
            'voucher_code' => null,
            'is_campus_price' => false,
            'status' => $idFaker->randomElement([
                'pending_payment',
                'confirmed',
                'checked_in',
                'completed',
                'cancelled',
                'expired',
                'failed',
            ]),
            'cancelled_at' => null,
            'cancellation_reason' => null,
            'notes' => $idFaker->optional()->sentence(),
        ];
    }
}