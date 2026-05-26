<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Indicator;

class IndicatorSeeder extends Seeder
{
    public function run(): void
    {
        $indicators = [
            
            [
                'code' => 'ema_20',
                'name' => 'EMA 20',
                'description' => 'Media móvil exponencial de 20 periodos',
                'handler' => 'ema',
                'config' => [
                    'period' => 20,
                    'source' => 'close',
                ],
            ],
            
            [
                'code' => 'ema_50',
                'name' => 'EMA 50',
                'description' => 'Media móvil exponencial de 50 periodos',
                'handler' => 'ema',
                'config' => [
                    'period' => 50,
                    'source' => 'close',
                ],
            ],
            
            [
                'code' => 'rsi_14',
                'name' => 'RSI 14',
                'description' => 'Índice de fuerza relativa de 14 periodos',
                'handler' => 'rsi',
                'config' => [
                    'period' => 14,
                ],
            ],
            
            [
                'code' => 'macd_default',
                'name' => 'MACD (12,26,9)',
                'description' => 'Moving Average Convergence Divergence estándar',
                'handler' => 'macd',
                'config' => [
                    'fast' => 12,
                    'slow' => 26,
                    'signal' => 9,
                    'source' => 'close',
                ],
            ],
            
            [
                'code' => 'atr_14',
                'name' => 'ATR 14',
                'description' => 'Average True Range de 14 periodos',
                'handler' => 'atr',
                'config' => [
                    'period' => 14,
                ],
            ],
            
            [
                'code' => 'bollinger_20_2',
                'name' => 'Bollinger Bands 20,2',
                'description' => 'Bandas de Bollinger (20 periodos, desviación 2)',
                'handler' => 'bollinger',
                'config' => [
                    'period' => 20,
                    'deviation' => 2,
                    'source' => 'close',
                ],
            ],
        ];
        
        foreach ($indicators as $indicator) {
            Indicator::updateOrCreate(
                [
                    'code' => $indicator['code']
                ],
                $indicator
            );
        }
    }
}