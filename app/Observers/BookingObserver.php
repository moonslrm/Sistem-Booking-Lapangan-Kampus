<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingObserver
{
    public function created(Booking $booking): void
    {
        ActivityLog::query()->create([
            'user_id' => Auth::id() ?? $booking->user_id,
            'action' => 'booking_status_created',
            'model_type' => Booking::class,
            'model_id' => $booking->id,
            'description' => 'Booking created with initial status '.$booking->status,
            'old_values' => null,
            'new_values' => ['status' => $booking->status],
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    public function updated(Booking $booking): void
    {
        if (! $booking->wasChanged('status')) {
            return;
        }

        ActivityLog::query()->create([
            'user_id' => Auth::id() ?? $booking->user_id,
            'action' => 'booking_status_updated',
            'model_type' => Booking::class,
            'model_id' => $booking->id,
            'description' => sprintf(
                'Booking status changed from %s to %s',
                $booking->getOriginal('status'),
                $booking->status
            ),
            'old_values' => ['status' => $booking->getOriginal('status')],
            'new_values' => ['status' => $booking->status],
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}