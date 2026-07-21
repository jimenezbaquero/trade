<?php

namespace App\Console\Commands;

use App\Domain\Decision\SimulatedDecisionEngine;
use App\Domain\Trading\Signal;
use App\Models\SimulatedDecision;
use App\Models\SimulatedSignal;
use App\Models\Simulation;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('simulation:decision {simulation_id}')]
#[Description('Run decision engine over simulated signals')]
class DecisionSimulated extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(SimulatedDecisionEngine $decisionEngine)
    {
        $doSignal = false;
        $openPosition1 = false;
        $openPosition2 = false;
        
        $account = 100;
        $account1 = 0;
        $account2 = 0;
        $priceBuy1 = null;
        $priceBuy2 = null;
        $priceSell = null;
        $sell = false;
        
        $simulationId = $this->argument('simulation_id');
        
        $simulation = Simulation::findOrFail($simulationId);
        
        
        // limpiar decisiones anteriores de esa simulación
        $signalIds = $simulation->simulatedSignals()->pluck('id');
        
        SimulatedDecision::whereIn('simulated_signal_id', $signalIds)->delete();
        
        $signals = SimulatedSignal::with('simulation')->where('simulation_id', $simulationId)
            ->orderBy('market_timestamp')
            ->get();
        
        $this->info("Processing {$signals->count()} simulated signals...");
        
        foreach ($signals as $signal) {
            $doSignal = false;
            
            $dto = new Signal(
                symbol: $signal->simulation->pair->symbol,
                timeframe: $signal->simulation->timeframe,
                type: $signal->type,
                price: (float) $signal->price,
                confidence: (float) $signal->confidence,
                strategy: $signal->strategy,
                marketTimestamp: $signal->market_timestamp,
                meta: $signal->meta ?? [],
                
            );
            
            $decision = $decisionEngine->evaluate($dto);
            
            if(!$openPosition1 && $decision->action == 'open_long'){
                $doSignal = true;
                $openPosition1 = true;
                $account1 = $account*2/3;
                $account -= $account1;
                $priceBuy1 = $signal->price;
                $this->info('Se invierte '.$account1.' a '.$priceBuy1);
            }else if(!$openPosition2 && $decision->action == 'open_long'){
                $doSignal = true;
                $openPosition2 = true;
                $account2 = $account;
                $account = 0;
                $priceBuy2 = $signal->price;
                $this->info('Se invierte '.$account2.' a '.$priceBuy2);
            }else if($openPosition1  && $decision->action == 'open_short') {
                $doSignal = true;
                $priceSell = $signal->price;
                $sell = true;
                $this->info('Se vende a '.$priceSell);
            }
                
            if($doSignal){
                
                SimulatedDecision::create([
                    'simulation_id' => $simulationId,
                    'simulated_signal_id' => $signal->id,
                    'symbol' => $signal->symbol,
                    'timeframe' => $signal->timeframe,
                    'action' => $decision->action,
                    'price' => $signal->price,
                    'confidence' => $decision->confidence ?? 0,
                    'strategy' => $signal->strategy,
                    'market_timestamp' => $signal->market_timestamp,
                    'meta' => $decision->meta ?? [],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                if($sell){
                    if($openPosition1){
                        $account += ($account1/$priceBuy1)*$priceSell;
                    }
                    if($openPosition2){
                        $account += ($account2/$priceBuy2)*$priceSell;
                    }
                    $priceBuy1 = 0;
                    $priceBuy2 = 0;
                    $priceSell = 0;
                    $account1 = 0;
                    $account2 = 0;
                    $sell = false;
                    $openPosition1 = false;
                    $openPosition2 = false;
                }
                
                $this->info('Cantidad acumulada: '.($account));
            }
            
        }
        
        $this->info('Simulation decisions completed.');
    }
}
