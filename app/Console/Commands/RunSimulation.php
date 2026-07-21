<?php

namespace App\Console\Commands;

use App\Models\Candle;
use App\Models\Indicator;
use App\Models\Simulation;
use App\Models\SimulatedSignal;
use Illuminate\Console\Command;
use App\Services\MarketContextFactory;

class RunSimulation extends Command
{
    protected $signature = 'simulation:run {simulationId}';
    
    protected $description = 'Run trading strategy simulation';
    
    public function handle(): int
    {
        $simulation = Simulation::findOrFail(
            $this->argument('simulationId')
        );
        
        $simulation->simulatedsignals()->delete();
        
        $this->info("Running simulation: {$simulation->name}");
        
        $strategyClass = '\\App\\Domain\\Strategies\\'.$simulation->strategy;
        $strategy = new $strategyClass();
        
        $candles = Candle::query()
            ->where('pair_id', $simulation->pair_id)
            ->where('timeframe', $simulation->timeframe)
            ->whereBetween('opened_at', [
                $simulation->started_at,
                $simulation->ended_at,
            ])
            ->orderBy('opened_at')
            ->cursor();
        
        $signalsCount = 0;
        
        foreach ($candles as $candle) {
            
            $indicators = Indicator::active()->pluck('id')->toArray();
            $indicatorValues = $candle->indicatorValues()->pluck('value','indicator_id')->toArray();
            $symbol = $candle->pair->symbol;
            $candleArray = $candle->toArray();
            $candleArray['symbol'] = $symbol;
            
            $context = app(\App\Domain\Market\MarketContextFactory::class)->buildFromCandle(
                $candleArray,
                $indicators,
                $indicatorValues
            );
            
            $signal = $strategy->evaluate($context);
            
            if (!$signal) {
                continue;
            }
            
            SimulatedSignal::create([
                'simulation_id' => $simulation->id,
                'pair_id' => $simulation->pair_id,
                'timeframe' => $simulation->timeframe,
                
                'type' => $signal->type,
                'price' => $signal->price,
                'confidence' => $signal->confidence,
                'strategy' => $strategyClass,
                
                'market_timestamp' => $candle->opened_at,
                'candle_id' => $candle->id,
                
                'meta' => $signal->meta,
            ]);
            
            $signalsCount++;
            
            if ($signalsCount % 100 === 0) {
                $this->info("Signals generated: {$signalsCount}");
            }
        }
        
        $simulation->update([
            'ended_at' => now(),
        ]);
        
        $this->info("Simulation completed. Signals: {$signalsCount}");
        
        return self::SUCCESS;
    }
}