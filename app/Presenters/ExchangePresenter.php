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
                'header' => 'Name',
                'sortable' => true,
                'filterable' => true,
                'type' => 'text',
            ],
            'slug' => [
                'key' => 'slug',
                'field' => 'slug',
                'header' => 'Slug',
                'sortable' => true,
                'filterable' => true,
                'type' => 'text',
            ],
            'is_active' => [
                'key' => 'is_active',
                'field' => 'is_active',
                'header' => 'Active',
                'sortable' => false,
                'filterable' => true,
                'type' => 'boolean',
            ],
        ];
    }
}