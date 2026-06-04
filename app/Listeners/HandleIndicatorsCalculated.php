<?php

namespace App\Listeners;

use App\Events\IndicatorsCalculated;
use App\Jobs\CalculateSignals;

class HandleIndicatorsCalculated
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
    public function handle(IndicatorsCalculated $event): void
    {
        CalculateSignals::dispatch($event->candleId)->onQueue('signals');
    }
}
