<?php

namespace App\Domain\Trading;

use Carbon\Carbon;

class Signal
{
    public function __construct(
        public string $symbol,
        public ?string $timeframe,
        public string $type, // buy | sell | hold
        public float $price,
        public float $confidence,
        public string $strategy,
        public Carbon $marketTimestamp,
        public array $meta = [],
        public string $status = 'generated'
    ) {}
    
    /**
     * Helper opcional: convierte a array listo para DB
     */
    public function toArray(): array
    {
        return [
            'symbol' => $this->symbol,
            'timeframe' => $this->timeframe,
            'type' => $this->type,
            'price' => $this->price,
            'confidence' => $this->confidence,
            'strategy' => $this->strategy,
            'market_timestamp' => $this->marketTimestamp,
            'meta' => json_encode($this->meta),
            'status' => $this->status,
        ];
    }
}