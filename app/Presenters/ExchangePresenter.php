<?php

namespace App\Presenters;

class ExchangePresenter
{
    public static function columns(): array
    {
        return [
            'name' => [
                'key' => 'name',
                'field' => 'name',
                'header' => 'exchange.fields.name',
                'sortable' => true,
                'filterable' => true,
                'type' => 'text',
                'width' => 'auto'
            ],
            'slug' => [
                'key' => 'slug',
                'field' => 'slug',
                'header' => 'exchange.fields.slug',
                'sortable' => true,
                'filterable' => true,
                'type' => 'text',
                'width' => '250px'
            ],
            'is_active' => [
                'key' => 'is_active',
                'field' => 'is_active',
                'header' => 'exchange.fields.is_active',
                'sortable' => false,
                'filterable' => false,
                'type' => 'funnel',
                'width' => '150px'
            ],
        ];
    }
}