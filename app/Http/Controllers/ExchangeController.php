<?php

namespace App\Http\Controllers;

use App\Models\Exchange;
use App\Presenters\ExchangePresenter;
use App\Services\ExchangeService;
use Illuminate\Http\Request;
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
            'columns' => ExchangePresenter::columns()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exchange $exchange)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exchange $exchange)
    {
        //
    }
    
    public function getData(Request $request){
        return $this->service->getData($request->all());
    }
    
    private function getFilters(Request $request): array
    {
        return [
            'slug' => [
                'value' => '',
                'type' => 'text',
                'field' => 'slug',
                'operator' => 'like',
                'order_direction' => ''
            ],
            'is_active' => [
                'value' => true,
                'type' => 'boolean',
                'field' => 'is_active',
                'operator' => '=',
                'order_direction' => ''
            ],
            'name' => [
                'value' => '',
                'type' => 'text',
                'field' => 'name',
                'operator' => 'like',
                'order_direction' => '',
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
