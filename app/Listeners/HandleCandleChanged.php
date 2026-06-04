<?php

namespace App\Listeners;

use App\Events\CandleChanged;
use App\Jobs\CalculateIndicatorValues;
use App\Models\Indicator;
use Illuminate\Support\Facades\Cache;

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
        $indicatorIds = Cache::remember('indicators_trading', 3600, function () {
            return Indicator::where('id','<',4)->pluck('id')->toArray();
        });
        
        CalculateIndicatorValues::dispatch($event->candleId, $indicatorIds)->onQueue('indicators');
    }
}
