<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use App\Services\SlotAvailabilityService;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'venue_id',
        'slot_id',
        'booking_date',
        'start_time',
        'end_time',
        'duration_hours',
        'price_per_hour',
        'total_price',
        'discount_amount',
        'final_price',
        'voucher_code',
        'is_campus_price',
        'status',
        'cancelled_at',
        'cancellation_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'cancelled_at' => 'datetime',
            'is_campus_price' => 'boolean',
            'duration_hours' => 'decimal:2',
            'price_per_hour' => 'decimal:2',
            'total_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'final_price' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Booking $booking): void {
            if (! empty($booking->booking_code)) {
                return;
            }

            do {
                $code = 'CSBS-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
            } while (static::where('booking_code', $code)->exists());

            $booking->booking_code = $code;
        });

        static::saved(function (Booking $booking): void {
            $booking->invalidateSlotCache();
        });

        static::deleted(function (Booking $booking): void {
            $booking->invalidateSlotCache();
        });
    }

    // Tip to avoid N+1: Booking::with(['user', 'venue', 'slot', 'payment', 'qrCode'])->get()
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(VenueSlot::class, 'slot_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(BookingPayment::class);
    }

    public function qrCode(): HasOne
    {
        return $this->hasOne(BookingQrCode::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['pending_payment', 'confirmed', 'checked_in']);
    }

    private function invalidateSlotCache(): void
    {
        if (! $this->booking_date || ! $this->venue_id) {
            return;
        }

        $service = app(SlotAvailabilityService::class);
        $service->invalidateCache($this->venue_id, $this->booking_date->toDateString());
    }

    public function scopeForDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('booking_date', [$startDate, $endDate]);
    }
}