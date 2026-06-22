<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Venue;
use App\Models\VenueClosure;
use Illuminate\Support\Facades\Cache;

class SlotAvailabilityService
{
    public function cacheKey(int $venueId, string $date): string
    {
        return "slot:{$venueId}:{$date}";
    }

    public function getCached(int $venueId, string $date): ?array
    {
        return Cache::get($this->cacheKey($venueId, $date));
    }

    public function invalidateCache(int $venueId, string $date): void
    {
        Cache::forget($this->cacheKey($venueId, $date));
    }

    public function getAvailableSlots(Venue $venue, string $date): array
    {
        $dayOfWeek = date('w', strtotime($date));
        $closures = VenueClosure::where('venue_id', $venue->id)
            ->whereDate('closed_date', $date)
            ->exists();

        $slots = $venue->slots()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        if ($closures) {
            return $slots->map(fn ($slot) => [
                'id' => $slot->id,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'price_normal' => $slot->price_normal,
                'price_campus' => $slot->price_campus,
                'status' => 'maintenance',
            ])->toArray();
        }

        $bookings = Booking::query()
            ->where('venue_id', $venue->id)
            ->whereDate('booking_date', $date)
            ->whereIn('status', ['pending_payment', 'confirmed', 'checked_in'])
            ->get(['start_time', 'end_time']);

        return $slots->map(function ($slot) use ($bookings) {
            $status = 'available';

            foreach ($bookings as $booking) {
                if ($slot->start_time < $booking->end_time && $slot->end_time > $booking->start_time) {
                    $status = 'booked';
                    break;
                }
            }

            return [
                'id' => $slot->id,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'price_normal' => $slot->price_normal,
                'price_campus' => $slot->price_campus,
                'status' => $status,
            ];
        })->toArray();
    }
}
