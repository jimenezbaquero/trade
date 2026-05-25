<?php

namespace App\Contracts;

interface ExchangeClientInterface
{
    public function klines(
        string $symbol,
        string $interval,
        int $startTime,
        int $endTime,
        int $limit = 1000
    ): array;
    
    public function exchangeInfo(): array;
}