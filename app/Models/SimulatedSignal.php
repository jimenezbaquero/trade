<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SimulatedSignal extends Model
{
    protected $fillable = [
        'simulation_id',
        'type',
        'price',
        'confidence',
        'strategy',
        'market_timestamp',
        'candle_id',
        'meta',
    ];
    
    protected $casts = [
        'market_timestamp' => 'datetime',
        'meta' => 'array',
        'price' => 'decimal:8',
        'confidence' => 'decimal:2',
    ];
    
    public function simulation(): BelongsTo {
        return $this->belongsTo(Simulation::class);
    }
    
    public function simulatedDecisions(): HasMany
    {
        return $this->hasMany(SimulatedDecision::class);
    }
    
    public function pair():BelongsTo
    {
        return $this->belongsTo(Pair::class);
    }
    
    public function candle():BelongsTo
    {
        return $this->belongsTo(Candle::class);
    }
}