<?php

namespace App\Http\Controllers;

use App\Helpers\FunnelHelper;
use App\Http\Requests\ExchangeRequest;
use App\Models\Exchange;
use App\Presenters\ExchangePresenter;
use App\Services\ExchangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ExchangeController extends Controller
{
    public function __construct(
        private ExchangeService $service
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
       $filters = $this->getFilters($request);
        
        return Inertia::render('Exchanges/Index', [
            'exchanges' =>$this->service->getData($request->all()),
            'filters' => $filters,
            'columns' => ExchangePresenter::columns(),
            'actions' => ['update', 'delete'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Exchanges/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ExchangeRequest $request)
    {
        try {
            $data = $request->validated();
            $this->service->store($data);
            return to_route('exchanges.index')
                ->with('success', __('exchange.messages.created_successfully'));
        }catch (\Throwable $e) {
                
                Log::error('Exchange store failed', [
                    'message' => $e->getMessage(),
                    'data' => $data,
                ]);
                
                return to_route('exchanges.create')
                    ->withInput()
                    ->with('error', __('exchange.messages.created_error'));
            }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Exchange $exchange)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exchange $exchange)
    {
        return Inertia::render('Exchanges/Edit',[
            'exchange' => $exchange
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ExchangeRequest $request, Exchange $exchange)
    {
        try {
            $data = $request->validated();
            $this->service->update($data,$exchange);
            return to_route('exchanges.index')
                ->with('success', __('exchange.messages.updated_successfully'));
        }catch (\Throwable $e) {
            
            Log::error('Exchange store failed', [
                'message' => $e->getMessage(),
                'data' => $data,
            ]);
            
            return to_route('exchanges.edit',$exchange->id)
                ->withInput()
                ->with('error', __('exchange.messages.updated_error'));
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exchange $exchange)
    {
        try {
            $this->service->destroy($exchange);
            return response()->json([
                'success' => true,
                'message' => __('app.delete_success'),
            ]);
        }catch (\Throwable $e) {
            
            Log::error('app.delete_error', [
                'message' => $e->getMessage(),
                'data' => $exchange,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('app.delete_error'),
            ], 500);
        }
    }
    
    public function getData(Request $request){
        return $this->service->getData($request->all());
    }
    
    private function getFilters(Request $request): array
    {
        return [
            'name' => [
                'value' => '',
                'type' => 'text',
                'field' => 'name',
                'operator' => 'like',
                'order_direction' => '',
            ],
            'slug' => [
                'value' => '',
                'type' => 'text',
                'field' => 'slug',
                'operator' => 'like',
                'order_direction' => ''
            ],
            'is_active' => [
                'value' => true,
                'type' => 'funnel',
                'field' => 'is_active',
                'operator' => '=',
                'order_direction' => '',
                'showFunnel' => false,
                'options' => FunnelHelper::getOptions('is_active'),
            ],
            'created_at' => [
                'value' => '',
                'type' => 'date',
                'field' => 'created_at',
                'operator' => '>=',
                'order_direction' => '',
            ],
        ];
    }
}
