<?php

namespace App\Jobs;

use App\Models\Candle;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvalidateCandleCache implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Candle $candle
    ) {}

    public function handle(): void
    {
        Cache::tags([
            "candles",
            "pair:{$this->candle->pair_id}:{$this->candle->timeframe}"
        ])->flush();
    }
}
