<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'tourist_id',
        'service_id',
        'booking_id',
        'rating',
        'comment',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function tourist()
    {
        return $this->belongsTo(User::class, 'tourist_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}