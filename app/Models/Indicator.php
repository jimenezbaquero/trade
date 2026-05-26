<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Indicator extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'code',
        'name',
        'description',
        'config',
        'handler',
    ];
    
    /**
     * Cast JSON → array automáticamente
     */
    protected $casts = [
        'config' => 'array',
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
}