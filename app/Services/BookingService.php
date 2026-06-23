<?php

namespace App\Services;

use App\Exceptions\BookingConflictException;
use App\Exceptions\BookingException;
use App\Jobs\ReleaseExpiredLockJob;
use App\Models\Booking;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueClosure;
use App\Models\VenueSlot;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Services\SlotLockService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingService
{
    protected array $activeStatuses = [
        'pending_payment',
        'confirmed',
        'checked_in',
    ];

    public function createBooking(User $user, array $attributes): Booking
    {
        $bookingDate = $attributes['booking_date'];
        $slotId = $attributes['slot_id'];

        if (! app(SlotLockService::class)->acquireLock($slotId, $bookingDate, $user->id)) {
            Log::channel('booking')->warning('Booking conflict detected: model lock failure.', [
                'slot_id' => $slotId,
                'booking_date' => $bookingDate,
                'user_id' => $user->id,
            ]);

            throw new BookingConflictException('Slot sedang diproses pengguna lain, silakan coba slot lain atau tunggu beberapa saat');
        }

        ReleaseExpiredLockJob::dispatch($slotId, $bookingDate, $user->id)
            ->delay(now()->addMinutes(10));

        return DB::transaction(function () use ($user, $attributes, $bookingDate): Booking {
            $slot = VenueSlot::query()
                ->where('id', $attributes['slot_id'])
                ->lockForUpdate()
                ->first();

            if (! $slot) {
                throw new BookingException('Selected slot not found.');
            }

            $venue = Venue::query()->find($attributes['venue_id']);

            if (! $venue) {
                throw new BookingException('Selected venue not found.');
            }

            if ($slot->venue_id !== $venue->id) {
                throw new BookingException('Selected slot does not belong to the chosen venue.');
            }

            if (! $slot->is_active) {
                throw new BookingException('Selected slot is inactive.');
            }

            if ((int) $slot->day_of_week !== (int) Carbon::parse($bookingDate)->dayOfWeek) {
                throw new BookingException('Selected slot is not available on the requested date.');
            }

            if (VenueClosure::where('venue_id', $venue->id)
                ->whereDate('closed_date', $bookingDate)
                ->exists()) {
                throw new BookingException('Venue is closed on the requested date.');
            }

            $this->assertSlotAvailable(
                $venue->id,
                $slot->start_time,
                $slot->end_time,
                $bookingDate
            );

            $pricePerHour = $slot->getPriceForUser($user);
            $durationHours = $this->calculateDurationHours($slot->start_time, $slot->end_time);
            $totalPrice = round($pricePerHour * $durationHours, 2);
            $discountAmount = 0.0;
            $voucherCode = $attributes['voucher_code'] ?? null;
            $voucher = null;

            if (! empty($voucherCode)) {
                $voucher = Voucher::query()->where('code', $voucherCode)->first();

                if (! $voucher || ! $voucher->isValid()) {
                    throw new BookingException('Voucher code is invalid or expired.');
                }

                if ($voucher->target_role !== 'all' && $voucher->target_role !== $user->role) {
                    throw new BookingException('Voucher is not valid for your user role.');
                }

                if ($totalPrice < $voucher->min_booking_amount) {
                    throw new BookingException('Booking minimum amount does not meet voucher requirements.');
                }

                if ($voucher->hasReachedLimit()) {
                    throw new BookingException('Voucher usage limit has been reached.');
                }

                $userUsageCount = VoucherUsage::query()
                    ->where('voucher_id', $voucher->id)
                    ->where('user_id', $user->id)
                    ->count();

                if ($voucher->max_per_user > 0 && $userUsageCount >= $voucher->max_per_user) {
                    throw new BookingException('You have already used this voucher the maximum number of times.');
                }

                if ($voucher->discount_type === 'percentage') {
                    $discountAmount = round($totalPrice * ($voucher->discount_value / 100), 2);
                } else {
                    $discountAmount = (float) $voucher->discount_value;
                }

                if ($voucher->max_discount_amount > 0) {
                    $discountAmount = min($discountAmount, (float) $voucher->max_discount_amount);
                }

                $discountAmount = min($discountAmount, $totalPrice);
            }

            $booking = Booking::query()->create([
                'user_id' => $user->id,
                'venue_id' => $venue->id,
                'slot_id' => $slot->id,
                'booking_date' => $bookingDate,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'duration_hours' => $durationHours,
                'price_per_hour' => $pricePerHour,
                'total_price' => $totalPrice,
                'discount_amount' => $discountAmount,
                'final_price' => max($totalPrice - $discountAmount, 0),
                'voucher_code' => $voucherCode,
                'is_campus_price' => $user->is_campus_member,
                'status' => 'pending_payment',
                'notes' => $attributes['notes'] ?? null,
            ]);

            if ($voucher) {
                VoucherUsage::query()->create([
                    'voucher_id' => $voucher->id,
                    'user_id' => $user->id,
                    'booking_id' => $booking->id,
                    'discount_amount' => $discountAmount,
                    'used_at' => now(),
                ]);
            }

            Log::channel('booking')->info('Booking created.', [
                'booking_id' => $booking->id,
                'slot_id' => $slot->id,
                'booking_date' => $bookingDate,
                'user_id' => $user->id,
                'status' => $booking->status,
            ]);

            return $booking;
        });
    }

    public function getUserBookings(User $user, int $perPage = 15)
    {
        return Booking::query()
            ->with(['venue', 'slot'])
            ->where('user_id', $user->id)
            ->orderByDesc('booking_date')
            ->paginate($perPage);
    }

    public function cancelBooking(User $user, Booking $booking, ?string $reason = null): Booking
    {
        if (! in_array($booking->status, ['pending_payment', 'confirmed'], true)) {
            throw new BookingException('Only pending or confirmed bookings can be cancelled.');
        }

        if ($booking->status === 'confirmed') {
            $slotStart = Carbon::parse(sprintf('%s %s', $booking->booking_date->toDateString(), $booking->start_time));
            $deadline = $slotStart->subHours(config('csbs.booking.cancellation_deadline_hours', 24));

            if (now()->greaterThan($deadline)) {
                throw new BookingException('Cancellation deadline has passed.');
            }
        }

        DB::transaction(function () use ($booking, $reason) {
            $booking->status = 'cancelled';
            $booking->cancelled_at = now();
            $booking->cancellation_reason = $reason;
            $booking->save();

            VoucherUsage::query()
                ->where('booking_id', $booking->id)
                ->delete();
        });

        app(SlotLockService::class)->releaseLock($booking->slot_id, $booking->booking_date->toDateString(), $booking->user_id);

        Log::channel('booking')->info('Booking cancelled.', [
            'booking_id' => $booking->id,
            'slot_id' => $booking->slot_id,
            'booking_date' => $booking->booking_date->toDateString(),
            'user_id' => $booking->user_id,
            'status' => $booking->status,
        ]);

        return $booking;
    }

    public function checkInBooking(Booking $booking): Booking
    {
        if ($booking->status !== 'confirmed') {
            throw new BookingException('Only confirmed bookings can be checked in.');
        }

        $booking->status = 'checked_in';
        $booking->save();

        return $booking;
    }

    public function completeReadyBookings(): int
    {
        $now = now();
        $today = $now->toDateString();

        $bookings = Booking::query()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where(function (Builder $query) use ($today, $now) {
                $query->whereDate('booking_date', '<', $today)
                    ->orWhere(function (Builder $query) use ($today, $now) {
                        $query->whereDate('booking_date', $today)
                            ->whereTime('end_time', '<=', $now->toTimeString());
                    });
            })
            ->get();

        foreach ($bookings as $booking) {
            $booking->status = 'completed';
            $booking->save();
        }

        return $bookings->count();
    }

    public function expireStaleBookings(): int
    {
        $timeoutMinutes = config('csbs.booking.payment_timeout_minutes', 15);

        $bookings = Booking::query()
            ->where('status', 'pending_payment')
            ->where('created_at', '<=', now()->subMinutes($timeoutMinutes))
            ->get();

        foreach ($bookings as $booking) {
            DB::transaction(function () use ($booking) {
                $booking->status = 'expired';
                $booking->save();

                VoucherUsage::query()
                    ->where('booking_id', $booking->id)
                    ->delete();
            });

            app(SlotLockService::class)->releaseLock($booking->slot_id, $booking->booking_date->toDateString(), $booking->user_id);

            Log::channel('booking')->info('Booking expired.', [
                'booking_id' => $booking->id,
                'slot_id' => $booking->slot_id,
                'booking_date' => $booking->booking_date->toDateString(),
                'user_id' => $booking->user_id,
                'status' => $booking->status,
            ]);
        }

        return $bookings->count();
    }

    protected function calculateDurationHours(string $startTime, string $endTime): float
    {
        return Carbon::parse($startTime)
            ->diffInMinutes(Carbon::parse($endTime)) / 60;
    }

    protected function assertSlotAvailable(int $venueId, string $startTime, string $endTime, string $bookingDate): void
    {
        $conflictExists = Booking::query()
            ->where('venue_id', $venueId)
            ->whereDate('booking_date', $bookingDate)
            ->whereIn('status', $this->activeStatuses)
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->lockForUpdate()
            ->exists();

        if ($conflictExists) {
            throw new BookingException('The selected slot is no longer available.');
        }
    }
}
