<?php

namespace App\Services;

use App\DTO\ExchangeQueryDTO;
use App\Filters\ExchangeFilter;
use App\Helpers\FilterHelper;
use App\Helpers\OrderHelper;
use App\Models\Exchange;

class ExchangeService
{
    public function getData(array $payload){
        $perPage = $payload['perPage'] ?? 3;
        $page = $payload['page'] ?? 1;
        
        $query = Exchange::query();

        $query = FilterHelper::applyFilter($query, $payload);
        $query = OrderHelper::makeOrder($query, $payload);
        
       return $query->paginate($perPage,['*'],'page',$page)->through(function ($exchange) {
                return [
                    'id' => $exchange->id,
                    'name' => $exchange->name,
                    'slug' => $exchange->slug,
                    'is_active' => $exchange->is_active? __('Yes') : __('No'),
                ];
            });
    }
    
}