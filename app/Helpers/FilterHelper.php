<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;

class FilterHelper
{
    public static function applyFilter(Builder $query, array $filters): Builder
    {
        foreach ($filters as $key => $filter) {
            if(in_array($key, ['page', 'limit', 'sort', 'order', 'perPage', 'sortField', 'sortOrder'])) {
                continue;
            }
            
            $type = $filter['type'] ?? 'text';
            $field = $filter['field'];
            $op = $filter['operation'] ?? '=';
            $value = $filter['value'];
            
            if ($type !== 'funnel' &&($value === null || $value === '')) {
                continue;
            }
            
            $query = match ($type) {
                
                'text' => self::applyTextFilter($query, $field, $value),
                
                'funnel' => self::applyFunnelFilter($query, $filter),
                
                'number' => self::applyNumberFilter($query, $field, $op, $value),
                
                default => $query->where($field, $op, $value),
            };
        }
        
        return $query;
    }
    
    private static function applyTextFilter(Builder $query, string $field, string $value): Builder
    {
        return $query->where($field, 'like', "%{$value}%");
    }
    
    private static function applyFunnelFilter(Builder $query, array $filter): Builder
    {
        $unique = true;
        foreach ($filter['options'] as $option) {
            if($option['checked'] || $option['checked'] === 'true') {
                if($unique) {
                    $query->where($filter['field'], $option['value']);
                    $unique = false;
                }else{
                    $query->orWhere($filter['field'], $option['value']);
                }
            }
        }
        return $query;
    }
    
    private static function applyNumberFilter(Builder $query, string $field, string $op, $value): Builder
    {
        return $query->where($field, $op, $value);
    }

}