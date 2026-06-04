<?php

namespace App\Services;

use App\Domain\Market\MarketContext;
use App\Models\Signal;

class StrategyService
{
    public function __construct(
        private iterable $strategies
    ) {}
    
    public function run(MarketContext $context): void
    {
        $signals = [];
        
        foreach ($this->strategies as $strategy) {
            $signal = $strategy->evaluate($context);
            
            if (!$signal) {
                continue;
            }
            
            $signals[] = $signal;
        }
        
        if (empty($signals)) {
            return;
        }
        
        Signal::upsert(
            array_map(
                fn ($signal) => $signal->toArray(),
                $signals
            ),
            ['symbol', 'timeframe', 'strategy', 'market_timestamp'],
            ['type', 'price', 'confidence', 'status', 'meta', 'updated_at']
        );
        
//        event(new SignalsCreated($signals));
    }
}