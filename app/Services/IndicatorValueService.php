<?php

namespace App\Services;

use App\Models\Candle;
use App\Models\Indicator;
use App\Models\IndicatorValue;
use App\Models\Pair;
use Illuminate\Support\Facades\Cache;

class IndicatorValueService
{
    /**
     * HISTÓRICO (con cache)
     */
    public function getIndicatorValues(
        Indicator $indicator,
        Candle    $from,
        Candle    $to
    ) {

        $values = IndicatorValue::query()
            ->where('indicator_id', $indicator->id)
            ->whereHas('candle', function ($q) use ($from, $to) {
                $q->where('timeframe', $from->timeframe)
                    ->whereBetween('candle_id', [$from->id, $to->id]);
            })
            ->with('candle:id,opened_at')
            ->orderBy(
                Candle::select('opened_at')
                    ->whereColumn('candles.id', 'indicator_values.candle_id')
            )
            ->get()
            ->map(function ($value) {
                return [
                    'time' => $value->candle->opened_at->timestamp,
                    'value' => $value->value,
                    'candle_id' => $value->candle->id,
                ];
            });
        
        return ['values' => $values];
    }
   
}