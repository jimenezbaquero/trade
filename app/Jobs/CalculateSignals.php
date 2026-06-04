<?php

namespace App\Jobs;

use App\Domain\Market\MarketContextFactory;
use App\Models\Candle;
use App\Models\Indicator;
use App\Services\StrategyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CalculateSignals implements ShouldQueue
{
    use Queueable;

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
    public function handle( MarketContextFactory $factory,
                            StrategyService $strategyService
    ): void
    {
        $candle= Candle::findOrFail($this->candleId);

        if($candle->is_closed) {
            $indicators = Indicator::where('id','<',4)->pluck('id')->toArray();
            $indicatorValues = $candle->indicatorValues()->pluck('value','indicator_id')->toArray();
            $symbol = $candle->pair->symbol;
            $candle = $candle->toArray();
            $candle['symbol'] = $symbol;
            $context = $factory->buildFromCandle($candle, $indicators, $indicatorValues);
            $strategyService->run($context);
        }
    }
    
    public function failed(\Throwable $exception): void
    {
        logger()->error('CalculateSignals failed', [
            'candle_id' => $this->candleId,
            'error' => $exception->getMessage(),
        ]);
    }
}
