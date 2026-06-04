<?php

namespace App\Services;

use App\Calculators\EmaCalculator;
use App\Calculators\RsiCalculator;
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
        $value = null;
        
        try {
            switch ($indicator->code) {
                case 'ema_20':
                    $value = EmaCalculator::calculate(
                        $candle->close,
                        $this->getPreviousValue($indicator_id, $candle),
                        20
                    );
                    break;
                case 'ema_50':
                    $value = EmaCalculator::calculate(
                        $candle->close,
                        $this->getPreviousValue($indicator_id, $candle),
                        50
                    );
                    break;
                case 'rsi_14':
                    $value = RsiCalculator::calculate(
                        $this->getLastCandles(15, $candle),
                        14
                    );
                    break;
                default:
                    throw new \Exception('Indicator not supported '.$indicator->code);
                    
            }
            
            IndicatorValue::updateOrCreate(
                [
                    'indicator_id' => $indicator_id,
                    'candle_id' => $candleId,
                ],
                [
                    'value' => $value,
                ]
            );
            
        }catch (\Throwable $e){
            Log::error($e->getMessage());
        }
    }
    
    private function getPreviousValue(int $indicatorId, Candle $candle){
        $prevCandle = Candle::where('timeframe', $candle->timeframe)
            ->where('pair_id', $candle->pair_id)
            ->where('opened_at','<', $candle->opened_at)
            ->orderBy('opened_at','desc')
            ->first();
        if(!$prevCandle){
            return null;
        }
        $indicatorValue = $prevCandle->indicatorValues()->where('indicator_id',$indicatorId)->first();
        if(!$indicatorValue){
            return null;
        }
        
        return $indicatorValue->value['value'];
    }
    
    private function getLastCandles(int $account, Candle $candle){
        $candles = Candle::query()
            ->where('pair_id', $candle->pair_id)
            ->where('timeframe', $candle->timeframe)
            ->where('opened_at', '<=', $candle->opened_at)
            ->orderBy('opened_at', 'desc')
            ->take($account)
            ->get()
            ->reverse()
            ->values();
        
        return $candles->pluck('close')->map(fn ($v) => (float) $v)->toArray();
    }
}