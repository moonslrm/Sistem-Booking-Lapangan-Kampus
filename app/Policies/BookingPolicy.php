<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->hasRole('admin') || $booking->user_id === $user->id;
    }

    public function cancel(User $user, Booking $booking): bool
    {
        if (! $this->view($user, $booking)) {
            return false;
        }

        return in_array($booking->status, ['pending_payment', 'confirmed'], true);
    }

    public function checkIn(User $user, Booking $booking): bool
    {
        if (! $this->view($user, $booking) && ! $user->hasRole('koorlap')) {
            return false;
        }

        return $booking->status === 'confirmed';
    }
}
