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

#[Signature('indicatorValues:calculate {--indicator=} {--truncate=}' )]
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
        $truncate = $this->option('truncate') == 'yes';
        if($truncate) {
            DB::table('indicator_values')->truncate();
        }
        
        if(!is_null($this->option('indicator'))){
            $indicator = Indicator::where('code',$this->option('indicator'))->first();
            if(!is_null($indicator)){
                $indicators = [$indicator];
            }
        }else{
            $indicators = Indicator::all();
        }
        
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
                foreach ($indicators as $indicator) {
                    $this->info('Calculando valores para indicador '.$indicator->code);
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
