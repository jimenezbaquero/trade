<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Simulation extends Model
{
    protected $fillable = [
        'name',
        'strategy',
        'pair_id',
        'timeframe',
        'started_at',
        'ended_at',
        'settings',
    ];
    
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'settings' => 'array',
    ];
    
    public function simulatedSignals(){
        return $this->hasMany(SimulatedSignal::class);
    }
    
    public function pair():belongsTo
    {
        return $this->belongsTo(Pair::class);
    }
}