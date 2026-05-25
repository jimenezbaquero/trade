<?php

namespace App\Services;

use App\Contracts\ExchangeClientInterface;
use App\Models\Exchange;
use Illuminate\Support\Facades\Http;

class BinanceClientService implements ExchangeClientInterface
{
    public function __construct(
        private Exchange $exchange
    )
    {}
    
    public function klines($symbol, $interval, $startTime, $endTime, $limit = 1000): array
    {
        
        
        $response = Http::get($this->exchange->getApiBaseUrl(false) . '/api/v3/klines', [
            'symbol' => strtoupper($symbol),
            'interval' => $interval,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'limit' => $limit,
        ]);
        
        if ($response->failed()) {
            throw new \Exception(
                'Binance API error: ' . $response->body()
            );
        }
        
        return $response->json();
    }
    
    public function exchangeInfo(): array
    {
       return [];
    }
}