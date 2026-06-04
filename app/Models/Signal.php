<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Signal extends Model
{
    protected $fillable = [
        'symbol',
        'type',
        'price',
        'confidence',
        'strategy',
        'timestamp',
        'meta',
    ];
    
    protected $casts = [
        'meta' => 'array',
        'timestamp' => 'datetime',
    ];
}
