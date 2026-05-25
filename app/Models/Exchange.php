<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    
    public function pairs(): HasMany
    {
        return $this->hasMany(Pair::class);
    }
    
    
    public function getApiBaseUrl(bool $testnet = false): string
    {
        return $testnet ? $this->testnet_api_url : $this->api_url;
    }
    
    public function getWebSocketUrl(bool $testnet = false): string
    {
        return $testnet ? $this->testnet_websocket_url : $this->websocket_url;
    }
    
    
}
