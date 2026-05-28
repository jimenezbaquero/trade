<?php

namespace App\Jobs;

use App\Models\Candle;
use App\Models\Indicator;
use App\Models\IndicatorValue;
use App\Services\IndicatorCalculatorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class CalculateIndicatorValues implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $candleId
    )
    {}
    
    /**
     * Execute the job.
     */
    public function handle(IndicatorCalculatorService $service): void
    {
        foreach (Indicator::all() as $indicator) {
            try {
                $value = $service->calculate($indicator->id, $this->candleId);
                
                IndicatorValue::updateOrCreate(
                    [
                        'indicator_id' => $indicator->id,
                        'candle_id' => $this->candleId,
                    ],
                    [
                        'value' => $value,
                    ]
                );
            } catch (\Throwable $e) {
            
            }
        }
    }
}
