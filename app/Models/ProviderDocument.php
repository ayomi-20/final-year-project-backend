<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderDocument extends Model
{
    use HasFactory;

    protected $fillable = ['provider_id', 'type', 'file_path'];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}