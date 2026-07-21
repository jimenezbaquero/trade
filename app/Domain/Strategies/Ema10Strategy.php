<?php

namespace App\Domain\Strategies;

use App\Contracts\StrategyInterface;
use App\Domain\Market\MarketContext;
use App\Domain\Trading\Signal;
use App\Models\Indicator;

class Ema10Strategy implements StrategyInterface
{
    public function evaluate(MarketContext $ctx): ?Signal
    {
        $ema10 = $ctx->value(Indicator::where('code', 'ema_10')->first()->id);
        $ema50 = $ctx->value(Indicator::where('code', 'ema_50')->first()->id);
      

        if ($ema10 === null || $ema50 === null) {
            return null;
        }
        
        $bullish = $ema10['value'] > $ema50['value'];
        $bearish = $ema10['value'] < $ema50['value'];
        
        $tendence = $ema10['previous_value'] > $ema10['value'] ? 'low' : 'high';
        
        /*
        -------------------------------------------------
        BUY SIGNAL
        -------------------------------------------------
        tendencia alcista
        -------------------------------------------------
        */
        if ($bullish && $tendence == 'high') {
            return new Signal(
                symbol: $ctx->symbol,
                timeframe: $ctx->timeframe ?? null,
                type: 'buy',
                price: $ctx->close,
                confidence: 75.0,
                strategy: 'ema_10',
                marketTimestamp: $ctx->timestamp,
                meta: [
                    'reason' => 'EMA bullish + tendence high',
                    'ema10' => $ema10,
                    'ema50' => $ema50,
                    'trend' => 'bullish',
                ],
                status: 'generated'
            );
        }
        
        /*
        -------------------------------------------------
        SELL SIGNAL
        -------------------------------------------------
        tendencia bajista + sobrecompra
        -------------------------------------------------
        */
        if ($bearish && $tendence == 'low') {
            return new Signal(
                symbol: $ctx->symbol,
                timeframe: $ctx->timeframe ?? null,
                type: 'sell',
                price: $ctx->close,
                confidence: 75.0,
                strategy: 'ema_10',
                marketTimestamp: $ctx->timestamp,
                meta: [
                    'reason' => 'EMA bearish + tendence low',
                    'ema10' => $ema10,
                    'ema50' => $ema50,
                    'trend' => 'bearish',
                ],
                status: 'generated'
            );
        }
        
        return null;
    }
}