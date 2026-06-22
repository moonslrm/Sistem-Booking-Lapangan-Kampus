<?php

namespace App\Models;

use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    use HasActivityLog;
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'min_booking_amount',
        'max_discount_amount',
        'max_total_usage',
        'max_per_user',
        'valid_from',
        'valid_until',
        'target_role',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_booking_amount' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'valid_from' => 'date',
            'valid_until' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $today = now()->toDateString();

        return $today >= $this->valid_from?->toDateString()
            && $today <= $this->valid_until?->toDateString();
    }

    public function hasReachedLimit(): bool
    {
        if ((int) $this->max_total_usage === 0) {
            return false;
        }

        return $this->usages()->count() >= (int) $this->max_total_usage;
    }
}