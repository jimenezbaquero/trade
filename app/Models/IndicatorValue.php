<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndicatorValue extends Model
{
    protected $fillable = [
        'indicator_id',
        'candle_id',
        'value',
    ];
    
    protected $casts = [
        'value' => 'array',
    ];
    
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    
    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class);
    }
    
    public function candle(): BelongsTo
    {
        return $this->belongsTo(Candle::class);
    }
}