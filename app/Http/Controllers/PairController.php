<?php

namespace App\Http\Controllers;

use App\Helpers\FunnelHelper;
use App\Http\Requests\PairRequest;
use App\Models\Exchange;
use App\Models\Pair;
use App\Presenters\PairPresenter;
use App\Services\PairService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PairController extends Controller
{
    public function __construct(
        private PairService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $this->getFilters($request);

        return Inertia::render('Pairs/Index', [
            'pairs' => $this->service->getData($request->all()),
            'filters' => $filters,
            'columns' => PairPresenter::columns(),
            'actions' => ['update', 'delete'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Pairs/Create',[
            'exchanges' => Exchange::active()->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PairRequest $request)
    {
        try {

            $data = $request->validated();

            $this->service->store($data);

            return to_route('pairs.index')
                ->with('success', __('pair.messages.created_successfully'));

        } catch (\Throwable $e) {

            Log::error('Pair store failed', [
                'message' => $e->getMessage(),
                'data' => $data ?? [],
            ]);

            return to_route('pairs.create')
                ->withInput()
                ->with('error', __('pair.messages.created_error'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pair $pair)
    {
        return Inertia::render('Pairs/Show', [
            'pair' => $pair->load('exchange'),
            'last_updated' => $pair->candles()->latest('opened_at')->first()->opened_at,
            'time_options' => [
                ['value' => '1m', 'label' => '1m'],
                ['value' => '5m', 'label' => '5m'],
                ['value' => '15m', 'label' => '15m'],
                ['value' => '1h', 'label' => '1h'],
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pair $pair)
    {
        return Inertia::render('Pairs/Edit', [
            'pair' => $pair,
            'exchanges' => Exchange::active()->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PairRequest $request, Pair $pair)
    {
        try {

            $data = $request->validated();

            $this->service->update($data, $pair);

            return to_route('pairs.index')
                ->with('success', __('pair.messages.updated_successfully'));

        } catch (\Throwable $e) {

            Log::error('Pair update failed', [
                'message' => $e->getMessage(),
                'data' => $data ?? [],
            ]);

            return to_route('pairs.edit', $pair->id)
                ->withInput()
                ->with('error', __('pair.messages.updated_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pair $pair)
    {
        try {

            $this->service->destroy($pair);

            return response()->json([
                'success' => true,
                'message' => __('app.delete_success'),
            ]);

        } catch (\Throwable $e) {

            Log::error('Pair delete failed', [
                'message' => $e->getMessage(),
                'data' => $pair,
            ]);

            return response()->json([
                'success' => false,
                'message' => __('app.delete_error'),
            ], 500);
        }
    }

    public function getData(Request $request)
    {
        return $this->service->getData($request->all());
    }

    private function getFilters(Request $request): array
    {
        return [

            'symbol' => [
                'value' => '',
                'type' => 'text',
                'field' => 'symbol',
                'operator' => 'like',
                'order_direction' => '',
            ],

            'base_asset' => [
                'value' => '',
                'type' => 'text',
                'field' => 'base_asset',
                'operator' => 'like',
                'order_direction' => '',
            ],

            'quote_asset' => [
                'value' => '',
                'type' => 'text',
                'field' => 'quote_asset',
                'operator' => 'like',
                'order_direction' => '',
            ],

            'price_precision' => [
                'value' => '',
                'type' => 'number',
                'field' => 'price_presicion',
                'operator' => '=',
                'order_direction' => '',
            ],

            'quantity_precision' => [
                'value' => '',
                'type' => 'number',
                'field' => 'quantity_precision',
                'operator' => '=',
                'order_direction' => '',
            ],

            'min_qty' => [
                'value' => '',
                'type' => 'number',
                'field' => 'min_qty',
                'operator' => '>=',
                'order_direction' => '',
            ],

            'max_qty' => [
                'value' => '',
                'type' => 'number',
                'field' => 'max_qty',
                'operator' => '<=',
                'order_direction' => '',
            ],

            'min_notional' => [
                'value' => '',
                'type' => 'number',
                'field' => 'min_notional',
                'operator' => '>=',
                'order_direction' => '',
            ],

            'is_active' => [
                'value' => '',
                'type' => 'funnel',
                'field' => 'is_active',
                'operator' => '=',
                'order_direction' => '',
                'showFunnel' => false,
                'options' => FunnelHelper::getOptions('is_active'),
            ],

        ];
    }
}
