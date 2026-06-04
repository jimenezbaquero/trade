<?php

namespace App\Domain\Strategies;

use App\Contracts\StrategyInterface;
use App\Domain\Market\MarketContext;
use App\Domain\Trading\Signal;
use App\Models\Indicator;

class EmaRsiStrategy implements StrategyInterface
{
    public function evaluate(MarketContext $ctx): ?Signal
    {
        $ema20 = $ctx->value(Indicator::where('code','ema_20')->first()->id);
        $ema50 = $ctx->value(Indicator::where('code','ema_50')->first()->id);
        $rsi   = $ctx->value(Indicator::where('code','rsi_14')->first()->id);
        
        if (!$ema20 || !$ema50 || !$rsi) {
            return null;
        }
        
        $bullish = $ema20 > $ema50;
        
        if ($bullish && $rsi < 30) {
            return new Signal(
                symbol: $ctx->symbol,
                timeframe: $ctx->timeframe ?? null,
                type: 'buy',
                price: $ctx->close,
                confidence: 75.0, // mejor en escala 0-100 según tu tabla
                strategy: 'ema_rsi',
                marketTimestamp: $ctx->timestamp,
                meta: [
                    'reason' => 'EMA bullish + RSI oversold',
                    'rsi' => $rsi,
                    'bullish' => $bullish,
                ],
                status: 'generated'
            );
        }
        
        return null;
    }
}