<?php

namespace App\Helpers;

class FunnelHelper
{
    public static function getOptions($field) {
        $options = [];
        switch ($field) {
            case('is_active'):
                $options = self::createBooleanOptions();
                break;
        }
        return $options;
    }
    
    private static function createBooleanOptions() {
        return [
            'yes' => [
                'label' => __('app.yes'),
                'value' => 1,
                'checked' => false
            ],
            'no' => [
                'label' => __('app.no'),
                'value' => 0,
                'checked' => false
            ]
        ];
    }
}