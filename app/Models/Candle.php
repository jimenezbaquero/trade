<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candle extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'pair_id',
        'timeframe',
        'opened_at',
        'closed_at',
        'is_closed',
        'open',
        'high',
        'low',
        'close',
        'volume',
        'quote_volume',
        'trades_count',
        'taker_buy_base_volume',
        'taker_buy_quote_volume',
    ];
    
    protected $casts = [
        // 🕒 tiempos
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        
        // booleano
        'is_closed' => 'boolean',
        
        // precios (DECIMAL -> string safe en Laravel)
        'open' => 'decimal:8',
        'high' => 'decimal:8',
        'low' => 'decimal:8',
        'close' => 'decimal:8',
        
        // volumen (más precisión)
        'volume' => 'decimal:12',
        'quote_volume' => 'decimal:12',
        'taker_buy_base_volume' => 'decimal:12',
        'taker_buy_quote_volume' => 'decimal:12',
        
        // entero
        'trades_count' => 'integer',
    ];
    
    public function pair(): BelongsTo
    {
        return $this->belongsTo(Pair::class);
    }
}

