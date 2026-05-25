<?php

namespace App\Managers;

use App\Contracts\ExchangeClientInterface;
use App\Models\Exchange;
use App\Services\BinanceClientService;

class ExchangeManager
{
    public function make(Exchange $exchange): ExchangeClientInterface
    {
        return match ($exchange->slug) {
            'binance' => new BinanceClientService($exchange),
            default => throw new \Exception("Exchange not supported"),
        };
    }
}