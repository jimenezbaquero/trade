<?php

namespace App\Helpers;

class OrderHelper
{
    public static function makeOrder($query, $payload){
        foreach($payload as $key => $item){
            if(in_array($key, ['page', 'limit', 'sort', 'order', 'perPage', 'sortField', 'sortOrder'])) {
                continue;
            }
            if(!is_null($item['order_direction']) && $item['order_direction'] != ''){
                $query->orderBy($item['field'], $item['order_direction']);
            }
        }
        return $query;
    }
}