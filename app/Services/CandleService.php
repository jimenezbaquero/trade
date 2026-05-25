<?php

namespace App\Services;

use App\Models\Pair;
use App\Models\Candle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class CandleService
    {
        private function queryCandles(
        Pair $pair,
        string $timeframe,
        ?string $from,
        ?string $to,
        ?bool $live = false
        ) {
            $query = Candle::query()
                ->where('pair_id', $pair->id)
                ->where('timeframe', $timeframe);
            
            if($live){
                $query= $query->orderBy('opened_at', 'desc')
                    ->limit(1);
            }else {
                $query = $query->when($from, fn($q) => $q->where('opened_at', '>=', $from))
                    ->when($to, fn($q) => $q->where('opened_at', '<=', $to))
                    ->orderBy('opened_at', 'asc');
            }

        return $query->get([
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
        ?string $from,
        ?string $to
    ): string {
        return "candles:{$pairId}:{$timeframe}:{$from}:{$to}";
    }

    public function getCandles(
        Pair $pair,
        string $timeframe = '1m',
        ?string $from = null,
        ?string $to = null
    ) {
        $cacheKey = $this->buildCacheKey($pair->id, $timeframe, $from, $to);

        return Cache::tags([
            "candles",
            "pair:{$pair->id}:{$timeframe}"
        ])->remember($cacheKey, now()->addSeconds(30), function () use (
            $pair,
            $timeframe,
            $from,
            $to
        ) {
            $candles = $this->queryCandles($pair, $timeframe, $from, $to);
            
            $lastUpdated = $candles->last()?->opened_at?->timestamp;
            
            $candles = $candles->map(fn ($c) => [
                'open' => $c->open,
                'high' => $c->high,
                'low' => $c->low,
                'close' => $c->close,
                'volume' => $c->volume,
                'opened_at' => $c->opened_at->timestamp,
                ])
                ->values()->toArray();
            
            return [
                'candles' => $candles,
                'last_updated' => $lastUpdated
            ];
        });
    }
    
    public function getCandlesLive(
        Pair $pair,
        string $timeframe = '1m',
    ) {
        $to = Carbon::now();
        switch ($timeframe){
            case '1m':
                $from = Carbon::now()->subMinutes(1);
                break;
            case '5m':
                $from = Carbon::now()->subMinutes(5);
                break;
            case '15m':
                $from = Carbon::now()->subMinutes(15);
                break;
            case '1h':
                $from = Carbon::now()->subHour();
                break;
        }
        
        $cacheKey = $this->buildCacheKey($pair->id, $timeframe, $from, $to);
        
        return Cache::tags([
            "candles",
            "pair:{$pair->id}:{$timeframe}"
        ])->remember($cacheKey, now()->addSeconds(30), function () use (
            $pair,
            $timeframe,
            $from,
            $to
        ) {
            $candles = $this->queryCandles($pair, $timeframe, $from, $to, true);
            
            $lastUpdated = $candles->last()?->opened_at?->timestamp;
            
            $candles = $candles->map(fn ($c) => [
                'open' => $c->open,
                'high' => $c->high,
                'low' => $c->low,
                'close' => $c->close,
                'volume' => $c->volume,
                'opened_at' => $c->opened_at->timestamp,
            ])
                ->values()->toArray();
            
            return [
                'candles' => $candles,
                'last_updated' => $lastUpdated
            ];
        });
    }
}
