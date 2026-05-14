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
            
            if ($value === null || $value === '') {
                continue;
            }
            
            $query = match ($type) {
                
                'text' => self::applyTextFilter($query, $field, $value),
                
                'boolean' => self::applyBooleanFilter($query, $field, $value),
                
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
    
    private static function applyBooleanFilter(Builder $query, string $field, bool $value): Builder
    {
        return $query->where($field, '=', $value);
    }
    
    private static function applyNumberFilter(Builder $query, string $field, string $op, $value): Builder
    {
        return $query->where($field, $op, $value);
    }

}