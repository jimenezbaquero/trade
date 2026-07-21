<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Indicator extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'code',
        'name',
        'description',
        'config',
        'handler',
        'is_active',
    ];
    
    /**
     * Cast JSON → array automáticamente
     */
    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];
    
    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    
    /**
     * Obtener valor seguro de la config
     */
    public function getConfig(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }
    
    /**
     * Ej: EMA, RSI, MACD
     */
    public function getCodeLabel(): string
    {
        return strtoupper($this->code);
    }
    
    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    
    public function scopeCode($query, string $code)
    {
        return $query->where('code', $code);
    }
    
    public function scopeHandler($query, string $handler)
    {
        return $query->where('handler', $handler);
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function indicatorValues():HasMany
    {
        return $this->hasMany(IndicatorValue::class);
    }
}