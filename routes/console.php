<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('candles:get binance BTCUSDT live')
    ->everyTenSeconds()
    ->withoutOverlapping()
    ->runInBackground();
