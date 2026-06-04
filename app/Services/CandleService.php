<?php

namespace App\Services;

use App\Models\Pair;
use App\Models\Candle;
use Carbon\Carbon;

class CandleService
{
    private function queryCandles(
        Pair    $pair,
        string  $timeframe,
        ?string $from,
        ?string $to,
        ?bool   $live = false
    ) {
        $query = Candle::query()
            ->where('pair_id', $pair->id)
            ->where('timeframe', $timeframe)
            ->orderBy('opened_at', 'desc');

        if ($live) {
            $query = $query->limit(2);
        } else {
            if (!$from) {
                $query = $query->limit(20000);
            } else {
                $query = $query->when($from, fn($q) => $q->where('opened_at', '>=', $from));
            }
            $query = $query->when($to, fn($q) => $q->where('opened_at', '<=', $to));
        }

        return $query->get([
            'id',
            'opened_at',
            'open',
            'high',
            'low',
            'close',
            'volume',
        ])->sortBy('opened_at')->values();
    }

    public function getCandles(
        Pair    $pair,
        string  $timeframe = '1m',
        ?string $from = null,
        ?string $to = null
    ) {


        $candles = $this->queryCandles($pair, $timeframe, $from, $to);

        $lastUpdated = $candles->last()?->opened_at?->timestamp;

        $candles = $candles->map(fn($c) => [
            'id' => $c->id,
            'open' => $c->open,
            'high' => $c->high,
            'low' => $c->low,
            'close' => $c->close,
            'volume' => $c->volume,
            'opened_at' => $c->opened_at->timestamp,
        ])->values()->toArray();

        return [
            'candles' => $candles,
            'last_updated' => $lastUpdated
        ];
    }

    public function getCandlesLive(
        Pair   $pair,
        string $timeframe = '1m',
    ) {
        $to = Carbon::now();

        switch ($timeframe) {
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
            default:
                $from = Carbon::now()->subMinutes(1);
                break;
        }

        $candles = $this->queryCandles($pair, $timeframe, $from, $to, true);

        $lastUpdated = $candles->first()?->opened_at?->timestamp;

        $candles = $candles->values();

        if ($candles->count() >= 2) {
            $aux = $candles[0];
            $candles[0] = $candles[1];
            $candles[1] = $aux;
        }

        $candles = $candles->map(fn($c) => [
            'id' => $c->id,
            'open' => $c->open,
            'high' => $c->high,
            'low' => $c->low,
            'close' => $c->close,
            'volume' => $c->volume,
            'opened_at' => $c->opened_at->timestamp,
        ])->values()->toArray();

        return [
            'candles' => $candles,
            'last_updated' => $lastUpdated
        ];
    }
}
