<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimulatedDecision extends Model
{
    protected $fillable = [
        'simulated_signal_id',
        'action',
        'price',
        'confidence',
        'strategy',
        'market_timestamp',
        'signal_id',
        'mode',
        'meta',
    ];
    
    protected $casts = [
        'price' => 'decimal:8',
        'confidence' => 'decimal:2',
        'market_timestamp' => 'datetime',
        'meta' => 'array',
    ];
    
    public function signal()
    {
        return $this->belongsTo(SimulatedSignal::class);
    }
}
