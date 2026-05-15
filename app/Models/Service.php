<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'category_id',
        'region_id',
        'title',
        'slug',
        'description',
        'price',
        'images',
        'is_featured',
        'rating_avg',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'images'      => 'array',
            'is_featured' => 'boolean',
            'price'       => 'decimal:2',
            'rating_avg'  => 'decimal:2',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}