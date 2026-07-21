<?php

namespace App\Console\Commands;

use App\Calculators\EmaCalculator;
use App\Calculators\RsiCalculator;
use App\Models\Candle;
use App\Models\Indicator;
use App\Models\IndicatorValue;
use App\Services\IndicatorCalculatorService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('indicatorValues:calculate {--indicator=} {--delete=}' )]
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
        $delete = $this->option('delete') == 'yes';

        if(!is_null($this->option('indicator'))){
            $indicator = Indicator::where('code',$this->option('indicator'))->first();
            if(!is_null($indicator)){
                $indicators = [$indicator];
                if($delete) {
                    $indicator->indicatorValues()->delete();
                }
            }
        }else{
            $indicators = Indicator::active()->get();
            if($delete) {
               foreach ($indicators as $indicator) {
                   $indicator->indicatorValues()->delete();
               }
                }
        }

        $timeframes = ['1m', '5m', '15m', '1h'];


        foreach ($timeframes as $timeframe) {

            $this->info("Procesando time {$timeframe}");

            $candles = DB::table('candles')
                ->select('id', 'close')
                ->where('timeframe', $timeframe)
                ->orderBy('opened_at')
                ->get();

            if ($candles->isEmpty()) {
                continue;
            }

            foreach ($indicators as $indicator) {

                $rows = [];
                $nRows = 0;

                $emaState = null;
                $rsiWindow = [];

                foreach ($candles as $candle) {

                    $nRows++;

                    switch ($indicator->code) {
                        
                        case 'ema_10':
                            $emaState = EmaCalculator::calculate(
                                $candle->close,
                                $emaState['value']??null,
                                10
                            );
                            
                            $value = $emaState;
                            break;

                        case 'ema_20':
                            $emaState = EmaCalculator::calculate(
                                $candle->close,
                                $emaState['value']??null,
                                20
                            );

                            $value = $emaState;
                            break;

                        case 'ema_50':
                            $emaState = EmaCalculator::calculate(
                                $candle->close,
                                $emaState['value']??null,
                                50
                            );

                            $value = $emaState;
                            break;

                        case 'rsi_14':

                            $rsiWindow[] = $candle->close;

                            if (count($rsiWindow) > 15) {
                                array_shift($rsiWindow);
                            }

                            $value = RsiCalculator::calculate($rsiWindow, 14);
                            break;

                        default:
                            $value = null;
                    }

                    $rows[] = [
                        'indicator_id' => $indicator->id,
                        'candle_id' => $candle->id,
                        'value' => json_encode($value),
                    ];

                    if (count($rows) >= 2000) {

                        $this->info("Guardando. Registros: {$nRows}");

                        IndicatorValue::upsert(
                            $rows,
                            ['indicator_id', 'candle_id'],
                            ['value']
                        );

                        $rows = [];
                    }
                }

                if (!empty($rows)) {
                    IndicatorValue::upsert(
                        $rows,
                        ['indicator_id', 'candle_id'],
                        ['value']
                    );
                }
            }
        }
        $this->info('Done.');
    }
}
