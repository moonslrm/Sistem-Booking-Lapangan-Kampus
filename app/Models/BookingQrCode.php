<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingQrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'token',
        'qr_image_path',
        'is_used',
        'scanned_at',
        'scanned_by',
    ];

    protected function casts(): array
    {
        return [
            'is_used' => 'boolean',
            'scanned_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function scanner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}