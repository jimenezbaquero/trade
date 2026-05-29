<?php

namespace App\Calculators;

class RsiCalculator
{
    public static function calculate(
        array $closes,
        int $period = 14
    ): array {
        
        if (count($closes) < ($period + 1)) {
            return ['value' => null];
        }
        
        $gains = [];
        $losses = [];
        
        for ($i = 1; $i < count($closes); $i++) {
            
            $diff = $closes[$i] - $closes[$i - 1];
            
            if ($diff >= 0) {
                $gains[] = $diff;
                $losses[] = 0;
            } else {
                $gains[] = 0;
                $losses[] = abs($diff);
            }
        }
        
        $avgGain = array_sum($gains) / $period;
        $avgLoss = array_sum($losses) / $period;
        
        if ($avgLoss == 0) {
            return ['value' => 100];
        }
        
        $rs = $avgGain / $avgLoss;
        
        $rsi = 100 - (100 / (1 + $rs));
        
        return [
            'value' => round($rsi, 2),
        ];
    }
}