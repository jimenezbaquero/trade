<?php

namespace App\Presenters;

class PairPresenter
{
    public static function columns(): array
    {
        return [

            'symbol' => [
                'key' => 'symbol',
                'field' => 'symbol',
                'header' => 'pair.fields.symbol',
                'sortable' => true,
                'filterable' => true,
                'type' => 'text',
                'width' => '200px',
            ],

            'base_asset' => [
                'key' => 'base_asset',
                'field' => 'base_asset',
                'header' => 'pair.fields.base_asset',
                'sortable' => true,
                'filterable' => true,
                'type' => 'text',
                'width' => '150px',
            ],

            'quote_asset' => [
                'key' => 'quote_asset',
                'field' => 'quote_asset',
                'header' => 'pair.fields.quote_asset',
                'sortable' => true,
                'filterable' => true,
                'type' => 'text',
                'width' => '150px',
            ],

            'price_precision' => [
                'key' => 'price_precision',
                'field' => 'price_precision',
                'header' => 'pair.fields.price_precision',
                'sortable' => true,
                'filterable' => false,
                'type' => 'number',
                'width' => '120px',
            ],

            'quantity_precision' => [
                'key' => 'quantity_precision',
                'field' => 'quantity_precision',
                'header' => 'pair.fields.quantity_precision',
                'sortable' => true,
                'filterable' => false,
                'type' => 'number',
                'width' => '120px',
            ],

            'min_qty' => [
                'key' => 'min_qty',
                'field' => 'min_qty',
                'header' => 'pair.fields.min_qty',
                'sortable' => true,
                'filterable' => false,
                'type' => 'number',
                'width' => '150px',
            ],

            'max_qty' => [
                'key' => 'max_qty',
                'field' => 'max_qty',
                'header' => 'pair.fields.max_qty',
                'sortable' => true,
                'filterable' => false,
                'type' => 'number',
                'width' => '150px',
            ],

            'min_notional' => [
                'key' => 'min_notional',
                'field' => 'min_notional',
                'header' => 'pair.fields.min_notional',
                'sortable' => true,
                'filterable' => false,
                'type' => 'number',
                'width' => '150px',
            ],

            'is_active' => [
                'key' => 'is_active',
                'field' => 'is_active',
                'header' => 'pair.fields.is_active',
                'sortable' => true,
                'filterable' => true,
                'type' => 'funnel',
                'width' => '120px',
            ],

        ];
    }
}
