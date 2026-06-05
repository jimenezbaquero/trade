<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simulation extends Model
{
    protected $fillable = [
        'name',
        'strategy',
        'symbol',
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
}