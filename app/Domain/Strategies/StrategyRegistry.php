<?php

namespace App\Domain\Strategies;

class StrategyRegistry
{
    public function get(): array
    {
        return [
            app(EmaRsiStrategy::class)
        ];
    }
}