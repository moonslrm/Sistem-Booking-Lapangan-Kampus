<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\BookingPayment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<BookingPayment>
 */
class BookingPaymentFactory extends Factory
{
    protected $model = BookingPayment::class;

    public function definition(): array
    {
        $booking = Booking::factory();
        return [
            'booking_id' => $booking,
            'payment_method' => 'snap',
            'payment_gateway' => 'midtrans',
            'gateway_order_id' => null,
            'gateway_transaction_id' => null,
            'snap_token' => null,
            'amount' => 50000,
            'status' => 'pending',
            'paid_at' => null,
            'expired_at' => null,
            'gateway_response' => null,
        ];
    }
}
