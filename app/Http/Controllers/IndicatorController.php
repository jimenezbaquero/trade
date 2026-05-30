<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndicatorRequest;
use App\Models\Indicator;
use App\Presenters\IndicatorPresenter;
use App\Services\IndicatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class IndicatorController extends Controller
{
    public function __construct(
        private IndicatorService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $this->getFilters($request);

        return Inertia::render('Indicators/Index', [
            'indicators' => $this->service->getData($request->all()),
            'filters' => $filters,
            'columns' => IndicatorPresenter::columns(),
            'actions' => ['update', 'delete'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Indicators/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IndicatorRequest $request)
    {
        try {

            $data = $request->validated();

            $this->service->store($data);

            return to_route('indicators.index')
                ->with('success', __('indicator.messages.created_successfully'));

        } catch (\Throwable $e) {

            Log::error('Indicator store failed', [
                'message' => $e->getMessage(),
                'data' => $data ?? [],
            ]);

            return to_route('indicators.create')
                ->withInput()
                ->with('error', __('indicator.messages.created_error'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Indicator $indicator)
    {
        return Inertia::render('Indicators/Show', [
            'indicator' => $indicator,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Indicator $indicator)
    {
        return Inertia::render('Indicators/Edit', [
            'indicator' => $indicator,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IndicatorRequest $request, Indicator $indicator)
    {
        try {

            $data = $request->validated();

            $this->service->update($data, $indicator);

            return redirect()
                ->route('indicators.index')
                ->setStatusCode(303)
                ->with('success', __('indicator.messages.updated_successfully'));

        } catch (\Throwable $e) {

            Log::error('Indicator update failed', [
                'message' => $e->getMessage(),
                'data' => $data ?? [],
            ]);

            return redirect()
                ->route('indicators.edit', $indicator->id)
                ->setStatusCode(303)
                ->withInput()
                ->with('error', __('indicator.messages.updated_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Indicator $indicator)
    {
        try {

            $this->service->destroy($indicator);

            return response()->json([
                'success' => true,
                'message' => __('app.delete_success'),
            ]);

        } catch (\Throwable $e) {

            Log::error('Indicator delete failed', [
                'message' => $e->getMessage(),
                'data' => $indicator,
            ]);

            return response()->json([
                'success' => false,
                'message' => __('app.delete_error'),
            ], 500);
        }
    }

    /**
     * API data endpoint (datatable / filters)
     */
    public function getData(Request $request)
    {
        return $this->service->getData($request->all());
    }

    /**
     * Filters (igual estilo que PairController)
     */
    private function getFilters(Request $request): array
    {
        return [

            'code' => [
                'value' => '',
                'type' => 'text',
                'field' => 'code',
                'operator' => 'like',
                'order_direction' => '',
            ],

            'name' => [
                'value' => '',
                'type' => 'text',
                'field' => 'name',
                'operator' => 'like',
                'order_direction' => '',
            ],

            'handler' => [
                'value' => '',
                'type' => 'text',
                'field' => 'handler',
                'operator' => '=',
                'order_direction' => '',
            ],

        ];
    }
}
