<?php

namespace App\Domain\Market;

use Carbon\Carbon;

class MarketContext
{
    public function __construct(
        public string $symbol,
        public string $timeframe,
        public Carbon $timestamp,
        
        public float $open,
        public float $high,
        public float $low,
        public float $close,
        public float $volume,
        public float $quote_volume,
        public float $trades_count,
        public float $taker_buy_base_volume,
        public float $taker_buy_quote_volume,
        
        public array $values = [],
        public array $meta = [],
    ) {}
    
    public function value(string $key, mixed $default = null): mixed
    {
        return $this->values[$key] ?? $default;
    }
}