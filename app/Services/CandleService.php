<?php

namespace App\Services;

use App\Models\Pair;
use App\Models\Candle;
use Illuminate\Support\Facades\Cache;

class CandleService
{
    private function queryCandles(
        Pair $pair,
        string $timeframe,
        int $limit,
        ?string $from,
        ?string $to
    ) {
        return Candle::query()
            ->where('pair_id', $pair->id)
            ->where('timeframe', $timeframe)
            ->when($from, fn ($q) => $q->where('opened_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('opened_at', '<=', $to))
            ->orderBy('opened_at', 'asc')
            ->limit($limit)
            ->get([
                'opened_at',
                'open',
                'high',
                'low',
                'close',
                'volume',
            ]);
    }

    private function buildCacheKey(
        int $pairId,
        string $timeframe,
        int $limit,
        ?string $from,
        ?string $to
    ): string {
        return "candles:{$pairId}:{$timeframe}:{$limit}:{$from}:{$to}";
    }

    public function getCandles(
        Pair $pair,
        string $timeframe = '1m',
        int $limit = 200,
        ?string $from = null,
        ?string $to = null
    ) {
        $cacheKey = $this->buildCacheKey($pair->id, $timeframe, $limit, $from, $to);

        return Cache::tags([
            "candles",
            "pair:{$pair->id}:{$timeframe}"
        ])->remember($cacheKey, now()->addSeconds(30), function () use (
            $pair,
            $timeframe,
            $limit,
            $from,
            $to
        ) {
            return $this->queryCandles($pair, $timeframe, $limit, $from, $to);
        });
    }
}
