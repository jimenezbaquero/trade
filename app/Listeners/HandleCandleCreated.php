<?php

namespace App\Listeners;

use App\Events\CandleCreated;
use App\Jobs\InvalidateCandleCache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleCandleCreated
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CandleCreated $event): void
    {
        InvalidateCandleCache::dispatch($event->candle);
    }
}
