<?php

namespace App\Presenters;

class IndicatorPresenter
{
    public static function columns(): array
    {
        return [
            
            'code' => [
                'key' => 'code',
                'field' => 'code',
                'header' => 'indicator.fields.code',
                'sortable' => true,
                'filterable' => true,
                'type' => 'text',
                'width' => '150px',
            ],
            
            'name' => [
                'key' => 'name',
                'field' => 'name',
                'header' => 'indicator.fields.name',
                'sortable' => true,
                'filterable' => true,
                'type' => 'text',
                'width' => '200px',
            ],
            
            'handler' => [
                'key' => 'handler',
                'field' => 'handler',
                'header' => 'indicator.fields.handler',
                'sortable' => true,
                'filterable' => true,
                'type' => 'text',
                'width' => '150px',
            ],
        
        ];
    }
}