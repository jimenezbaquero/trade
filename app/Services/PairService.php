<?php

namespace App\Services;

use App\Helpers\FilterHelper;
use App\Helpers\OrderHelper;
use App\Models\Pair;

class PairService
{
    public function getData(array $payload)
    {
        $perPage = $payload['perPage'] ?? 10;
        $page = $payload['page'] ?? 1;
        
        $query = Pair::query();
        
        $query = FilterHelper::applyFilter($query, $payload);
        $query = OrderHelper::makeOrder($query, $payload);
        
        return $query
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(function ($pair) {
                return [
                    'id' => $pair->id,
                    'exchange_id' => $pair->exchange_id,
                    'symbol' => $pair->symbol,
                    'base_asset' => $pair->base_asset,
                    'quote_asset' => $pair->quote_asset,
                    'status' => $pair->status,
                    'price_precision' => $pair->price_precision,
                    'quantity_precision' => $pair->quantity_precision,
                    'min_qty' => $pair->min_qty,
                    'max_qty' => $pair->max_qty,
                    'is_active' => $pair->is_active ? __('app.yes') : __('app.no'),
                ];
            });
    }
    
    public function store(array $data)
    {
        $data['symbol'] = strtoupper($data['symbol']);
        
        $data['is_active'] = $data['is_active'] ?? true;
        
        Pair::create($data);
    }
    
    public function update(array $data, Pair $pair)
    {
        $data['symbol'] = strtoupper($data['symbol']);
        
        $data['is_active'] = $data['is_active'] ?? true;
        
        $pair->update($data);
    }
    
    public function destroy(Pair $pair)
    {
        $pair->delete();
    }
}