<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('candles:get binance BTCUSDT 1m live')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('candles:get binance BTCUSDT 5m live')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('candles:get binance BTCUSDT 15m live')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('candles:get binance BTCUSDT 1h live')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();
