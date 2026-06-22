<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VenueClosure extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id',
        'closed_date',
        'reason',
        'closed_by',
    ];

    protected function casts(): array
    {
        return [
            'closed_date' => 'date',
        ];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}