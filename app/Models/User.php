<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'role',
        'is_campus_member',
        'is_active',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_campus_member' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // Tip to avoid N+1: User::with(['bookings.venue', 'managedVenues'])->find($id)
    public function campusVerification(): HasOne
    {
        return $this->hasOne(UserCampusVerification::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function fcmTokens(): HasMany
    {
        return $this->hasMany(UserFcmToken::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function managedVenues(): HasMany
    {
        return $this->hasMany(Venue::class, 'managed_by');
    }

    public function isWaban(): bool
    {
        return $this->role === 'waban';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKoorlap(): bool
    {
        return $this->role === 'koorlap';
    }
}
