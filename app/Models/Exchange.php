<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'api_url',
        'testnet_api_url',
        'websocket_url',
        'testnet_websocket_url',
        'rate_limit',
        'metadata',
        'is_active',
    ];
    
    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
