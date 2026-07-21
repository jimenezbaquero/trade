<?php

namespace App\Domain\Decision;

use App\Contracts\DecisionInterface;
use App\Domain\Trading\Signal;


class SimulatedDecisionEngine implements DecisionInterface
{
    public function evaluate(Signal $signal): Decision
    {
        if ($signal->type === 'buy') {
            return new Decision(
                action: 'open_long',
                reason: 'buy signal'
            );
        }
        
        if ($signal->type === 'sell') {
            return new Decision(
                action: 'open_short',
                reason: 'sell signal'
            );
        }
        
        return new Decision(
            action: 'ignore',
            reason: 'no valid signal'
        );
    }
}