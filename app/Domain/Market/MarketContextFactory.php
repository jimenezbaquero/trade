<?php

namespace App\Domain\Market;

use App\Models\Candle;
use App\Models\Indicator;
use App\Models\IndicatorValue;
use Carbon\Carbon;

class MarketContextFactory
{
    public function buildFromCandle(array $candle, array $indicators, array $indicatorValues): MarketContext
    {
        $values = [];

        foreach ($indicators as $indicator) {
            $values[$indicator] = $indicatorValues[$indicator]['value'];
        }
        
        return new MarketContext(
            symbol: $candle['symbol'],
            timeframe: $candle['timeframe'],
            timestamp: Carbon::parse($candle['opened_at']),
            
            open: $candle['open'],
            high: $candle['high'],
            low: $candle['low'],
            close: $candle['close'],
            volume: $candle['volume'],
            quote_volume: $candle['quote_volume'],
            trades_count: $candle['trades_count'],
            taker_buy_base_volume: $candle['taker_buy_base_volume'],
            taker_buy_quote_volume: $candle['taker_buy_quote_volume'],
            
            values: $values
        );
    }
}