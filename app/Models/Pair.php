<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pair extends Model
{
    protected $fillable = [
        'exchange_id',
        
        'base_asset',
        'quote_asset',
        'symbol',
        
        'status',
        
        'price_precision',
        'quantity_precision',
        
        'min_qty',
        'max_qty',
        
        'tick_size',
        'step_size',
        
        'min_notional',
        
        'metadata',
        
        'is_active',
    ];
    
    protected $casts = [
        'metadata' => 'array',
        
        'is_active' => 'boolean',
        
        'min_qty' => 'decimal:8',
        'max_qty' => 'decimal:8',
        
        'tick_size' => 'decimal:8',
        'step_size' => 'decimal:8',
        
        'min_notional' => 'decimal:16',
    ];
    
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    
    public function exchange(): BelongsTo
    {
        return $this->belongsTo(Exchange::class);
    }
    
    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    
    public function getDisplayNameAttribute(): string
    {
        return "{$this->base_asset}/{$this->quote_asset}";
    }
    
    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeTrading($query)
    {
        return $query->where('status', 'TRADING');
    }
}