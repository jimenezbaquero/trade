<?php

namespace App\Contracts;

use App\Domain\Market\MarketContext;
use App\Domain\Trading\Signal;


interface StrategyInterface
{
    public function evaluate(MarketContext $context): ?Signal;
}