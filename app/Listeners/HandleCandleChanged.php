<?php

namespace App\Listeners;

use App\Events\CandleChanged;
use App\Jobs\CalculateIndicatorValues;

class HandleCandleChanged
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
    public function handle(CandleChanged $event): void
    {
        CalculateIndicatorValues::dispatch($event->candleId)->onQueue('indicators');
    }
}
