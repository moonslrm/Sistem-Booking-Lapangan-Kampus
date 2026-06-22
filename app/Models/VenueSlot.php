<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VenueSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id',
        'day_of_week',
        'start_time',
        'end_time',
        'price_normal',
        'price_campus',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_normal' => 'decimal:2',
            'price_campus' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'slot_id');
    }

    public function getPriceForUser(User $user): float
    {
        return $user->is_campus_member ? (float) $this->price_campus : (float) $this->price_normal;
    }
}