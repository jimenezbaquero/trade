<?php

namespace App\Contracts;


use App\Domain\Decision\Decision;
use App\Domain\Trading\Signal;


interface DecisionInterface
{
    public function evaluate(Signal $signal): Decision;
}