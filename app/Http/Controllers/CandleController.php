<?php

namespace App\Http\Controllers;

use App\Services\CandleService;
use App\Models\Pair;
use Illuminate\Http\Request;

class CandleController extends Controller
{
    public function __construct(
        private CandleService $service
    ) {}

    public function getCandles(Request $request, Pair $pair)
    {
        return response()->json(
            $this->service->getCandles(
                pair: $pair,
                timeframe: $request->input('timeframe', '1m'),
                from: $request->input('from'),
                to: $request->input('to'),
            )
        );
    }
    
    public function getCandlesLive(Request $request, Pair $pair)
    {
        return response()->json(
            $this->service->getCandlesLive(
                pair: $pair,
                timeframe: $request->input('timeframe', '1m'),
            )
        );
    }
}
