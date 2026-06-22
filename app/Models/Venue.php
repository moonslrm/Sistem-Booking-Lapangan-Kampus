<?php

namespace App\Models;

use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Venue extends Model
{
    use HasActivityLog;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'sport_type',
        'description',
        'location',
        'facilities',
        'managed_by',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'facilities' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Venue $venue): void {
            if (empty($venue->slug)) {
                $venue->slug = static::generateUniqueSlug($venue->name);
            }
        });
    }

    protected static function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function photos(): HasMany
    {
        return $this->hasMany(VenuePhoto::class);
    }

    public function slots(): HasMany
    {
        return $this->hasMany(VenueSlot::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'managed_by');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function closures(): HasMany
    {
        return $this->hasMany(VenueClosure::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeBySportType(Builder $query, string $sportType): Builder
    {
        return $query->where('sport_type', $sportType);
    }
}