<?php

namespace App\Observers;

use App\Events\CandleCreated;
use App\Models\Candle;

class CandleObserver
{
    /**
     * Handle the Candle "created" event.
     */
    public function created(Candle $candle): void
    {
        CandleCreated::dispatch($candle);
    }


}
