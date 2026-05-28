<?php

namespace App\Services;

use App\Calculators\EmaCalculator;
use App\Models\Indicator;
use App\Models\Candle;
use App\Models\IndicatorValue;
use Illuminate\Support\Facades\Log;

class IndicatorCalculatorService
{
    public function calculate(
        int $indicator_id,
        int $candleId
    ) {
        $candle = Candle::findOrFail($candleId);
        $indicator = Indicator::findOrFail($indicator_id);
        
        switch ($indicator->code) {
            case 'ema_20':
                return EmaCalculator::calculate(
                    $candle->close,
                    $this->getPreviousValue($indicator_id, $candle),
                    20
                );
            default:
                throw new \Exception('Indicator not supported');
        }
    }
    
    private function getPreviousValue(int $indicatorId, Candle $candle){
        $prevCandle = Candle::where('timeframe', $candle->timeframe)->where('opened_at','<', $candle->opened_at)->orderBy('opened_at','desc')->first();
        if(!$prevCandle){
            return null;
        }
        $indicatorValue = $prevCandle->indicatorValues()->where('indicator_id',$indicatorId)->first();
        if(!$indicatorValue){
            return null;
        }
        
        return $indicatorValue->value['value'];

    }
}