<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Candle;
use App\Models\Indicator;
use App\Models\IndicatorValue;

class IndicatorValueSeeder extends Seeder
{
    public function run(): void
    {
        $emaIndicator = Indicator::where('code', 'ema_20')->first();
        
        if (!$emaIndicator) {
            $this->command->warn('EMA indicator not found, skipping...');
            return;
        }
        
        $timeframes = ['1m', '5m', '15m', '1h'];
        
        $period = 20;
        $k = 2 / ($period + 1);
        
        foreach ($timeframes as $timeframe) {
            
            $this->command->info("Seeding EMA for timeframe: {$timeframe}");
            
            $candles = Candle::query()
                ->where('timeframe', $timeframe)
                ->orderBy('opened_at')
                ->get();
            
            if ($candles->isEmpty()) {
                $this->command->warn("No candles for {$timeframe}");
                continue;
            }
            
            $previousEma = null;
            
            foreach ($candles as $candle) {
                
                $close = (float) $candle->close;
                
                if ($previousEma === null) {
                    $ema = $close;
                } else {
                    $ema = ($close * $k) + ($previousEma * (1 - $k));
                }
                
                $previousEma = $ema;
                
                IndicatorValue::updateOrCreate(
                    [
                        'indicator_id' => $emaIndicator->id,
                        'candle_id' => $candle->id,
                    ],
                    [
                        'value' => [
                            'value' => round($ema, 4),
                        ],
                        'updated_at' => now(),
                    ]
                );
            }
        }
        
        $this->command->info('Indicator values seeded successfully for all timeframes.');
    }
}