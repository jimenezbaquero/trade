<?php

namespace App\Services;

use App\DTO\ExchangeQueryDTO;
use App\Filters\ExchangeFilter;
use App\Helpers\FilterHelper;
use App\Helpers\OrderHelper;
use App\Models\Exchange;
use Illuminate\Support\Str;

class ExchangeService
{
    public function getData(array $payload){
        $perPage = $payload['perPage'] ?? 10;
        $page = $payload['page'] ?? 1;

        $query = Exchange::query();

        $query = FilterHelper::applyFilter($query, $payload);
        $query = OrderHelper::makeOrder($query, $payload);

       return $query->paginate($perPage,['*'],'page',$page)->through(function ($exchange) {
                return [
                    'id' => $exchange->id,
                    'name' => $exchange->name,
                    'slug' => $exchange->slug,
                    'is_active' => $exchange->is_active? __('app.yes') : __('app.no'),
                ];
            });
    }
    
    public function store(array $data){
            $data['slug'] = Str::slug($data['slug']);
            
            $data['is_active'] = $data['is_active'] ?? true;
            
            Exchange::create($data);
    }
    
    public function update(array $data,Exchange $exchange){
        $data['slug'] = Str::slug($data['slug']);
        
        $data['is_active'] = $data['is_active'] ?? true;
        
        $exchange->update($data);
    }
    
    public function destroy(Exchange $exchange){
        $exchange->delete();
    }
}