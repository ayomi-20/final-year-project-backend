<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'contact',
        'password',
        'role',
        'avatar',
        'login_otp',
        'login_otp_created_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'login_otp',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'login_otp_created_at' => 'datetime',
            'password'             => 'hashed',
        ];
    }

    // ── Filament required methods ──────────────────────────────────────────

public function canAccessPanel(Panel $panel): bool
{
    return $this->role === 'admin';
}

public function getFilamentName(): string
{
    return $this->first_name . ' ' . $this->last_name;
}

    // ── Relationships ──────────────────────────────────────────────────────

    // A user can have one provider profile
    public function provider()
    {
        return $this->hasOne(Provider::class);
    }

    // A user (as tourist) can have many bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'tourist_id');
    }

    // A user (as tourist) can write many reviews
    public function reviews()
    {
        return $this->hasMany(Review::class, 'tourist_id');
    }

    // ── Role helpers ───────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isProvider(): bool
    {
        return $this->role === 'provider';
    }

    public function isTourist(): bool
    {
        return $this->role === 'tourist';
    }
}