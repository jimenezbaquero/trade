<?php

namespace App\Services;

use App\Helpers\FilterHelper;
use App\Helpers\OrderHelper;
use App\Models\Indicator;

class IndicatorService
{
    public function getData(array $payload)
    {
        $perPage = $payload['perPage'] ?? 10;
        $page = $payload['page'] ?? 1;
        
        $query = Indicator::query();
        
        $query = FilterHelper::applyFilter($query, $payload);
        $query = OrderHelper::makeOrder($query, $payload);
        
        return $query
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(function ($indicator) {
                return [
                    'id' => $indicator->id,
                    'code' => $indicator->code,
                    'name' => $indicator->name,
                    'description' => $indicator->description,
                    'handler' => $indicator->handler,
                    'config' => $indicator->config,
                    'is_active' => $indicator->is_active? __('app.yes') : __('app.no'),
                    
                    // útil para UI
                    'config_label' => $this->formatConfigLabel($indicator->config),
                ];
            });
    }
    
    public function store(array $data)
    {
        $data['code'] = strtolower($data['code']);
        $data['is_active'] = $data['is_active'] ?? true;
        
        $indicator = Indicator::create($data);
        
        return $indicator;
    }
    
    public function update(array $data, Indicator $indicator)
    {
        $data['code'] = strtolower($data['code']);
        
        $indicator->update($data);
        
        return $indicator;
    }
    
    public function destroy(Indicator $indicator)
    {
        $indicator->delete();
    }
    
    /*
    |--------------------------------------------------------------------------
    | Helpers internos
    |--------------------------------------------------------------------------
    */
    
    private function formatConfigLabel(array $config): string
    {
        if (empty($config)) {
            return '-';
        }
        
        return collect($config)
            ->map(fn ($value, $key) => "{$key}: {$value}")
            ->implode(' | ');
    }
}