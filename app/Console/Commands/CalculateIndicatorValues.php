<?php

namespace App\Console\Commands;

use App\Models\Candle;
use App\Models\Indicator;
use App\Models\IndicatorValue;
use App\Services\IndicatorCalculatorService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('indicatorValues:calculate')]
#[Description('Command description')]
class CalculateIndicatorValues extends Command
{
    public function __construct(
        protected IndicatorCalculatorService $service
    )
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        DB::table('indicator_values')->truncate();
        
        $timeframes = ['1m', '5m', '15m', '1h'];
        

        foreach ($timeframes as $timeframe) {
            $this->info('Procesando time '.$timeframe);
            $candles = Candle::query()
                ->where('timeframe', $timeframe)
                ->orderBy('opened_at')
                ->get();
            
            if ($candles->isEmpty()) {
                continue;
            }
            
            foreach ($candles as $candle) {
                foreach (Indicator::all() as $indicator) {
                    $this->info('Calculando valores para indicador '.$indicator->id);
                    try {
                        $value = $this->service->calculate($indicator->id, $candle->id);
                        
                        IndicatorValue::updateOrCreate(
                            [
                                'indicator_id' => $indicator->id,
                                'candle_id' => $candle->id,
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
        $this->info('Done.');
    }
}
