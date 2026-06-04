<?php

namespace App\Jobs;

use App\Events\IndicatorsCalculated;
use App\Models\Candle;
use App\Models\Indicator;
use App\Models\IndicatorValue;
use App\Services\IndicatorCalculatorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CalculateIndicatorValues implements ShouldQueue
{
    use Dispatchable, Queueable;
    
    /**
     * Create a new job instance.
     */
    public function __construct(
        public int   $candleId,
        public array $indicators,
    ) {
    }
    
    /**
     * Execute the job.
     */
    public function handle(IndicatorCalculatorService $service): void {
        foreach ($this->indicators as $indicator) {
            try {
                $service->calculate($indicator, $this->candleId);
            } catch (\Throwable $e) {
                Log::error($e->getMessage());
            }
        }
        event(new IndicatorsCalculated(
            candleId: $this->candleId
        ));
        
    }
}
