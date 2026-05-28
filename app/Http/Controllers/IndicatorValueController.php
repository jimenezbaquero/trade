<?php

namespace App\Http\Controllers;

use App\Models\Candle;
use App\Models\Indicator;
use App\Services\IndicatorValueService;
use App\Models\Pair;
use Illuminate\Http\Request;

class IndicatorValueController extends Controller
{
    public function __construct(
        private IndicatorValueService $service
    ) {}
    
    public function getIndicatorValues(Indicator $indicator, Request $request)
    {
        return response()->json(
            $this->service->getIndicatorValues(
                indicator: $indicator,
                from: Candle::find($request->input('from')),
                to: Candle::find($request->input('to')),
            )
        );
    }
    
}
