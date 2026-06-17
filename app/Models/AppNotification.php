<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'is_admin',
        'type',
        'title',
        'body',
        'is_read',
        'related_id',
        'related_type',
    ];

    protected function casts(): array
    {
        return [
            'is_admin' => 'boolean',
            'is_read'  => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}