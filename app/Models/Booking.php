<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'tourist_id',
        'service_id',
        'persons',
        'notes',
        'status',
        'cancellation_reason',
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

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}