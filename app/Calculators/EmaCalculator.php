<?php

namespace App\Calculators;

class EmaCalculator
{
    public static function calculate(
        float $candleClose,
        ?float $previousValue,
        int $period
    ): array {
        
        $k = 2 / ($period + 1);
        
        if ($previousValue === null) {
            return ['value' => $candleClose];
        }
        
        return (
            ['value' => ($candleClose * $k) + ($previousValue * (1 - $k))]
        );
    }
}