<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'contact',
        'password',
        'role',
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
            'email_verified_at'  => 'datetime',
            'login_otp_created_at' => 'datetime',
            'password'           => 'hashed',
        ];
    }
}