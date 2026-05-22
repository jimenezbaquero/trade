<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PairRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $pairId = $this->route('pair')?->id;
        
        return [
            
            'exchange_id' => [
                'required',
                'exists:exchanges,id',
            ],
            
            'base_asset' => [
                'required',
                'string',
                'max:20',
            ],
            
            'quote_asset' => [
                'required',
                'string',
                'max:20',
            ],
            
            'symbol' => [
                'required',
                'string',
                'max:50',
                
                Rule::unique('pairs')
                    ->where(function ($query) {
                        return $query->where(
                            'exchange_id',
                            $this->exchange_id
                        );
                    })
                    ->ignore($pairId),
            ],
            
            'status' => [
                'nullable',
                'string',
                'max:50',
            ],
            
            'price_precision' => [
                'nullable',
                'integer',
                'min:0',
            ],
            
            'quantity_precision' => [
                'nullable',
                'integer',
                'min:0',
            ],
            
            'min_qty' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            
            'max_qty' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            
            'tick_size' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            
            'step_size' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            
            'min_notional' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            
            'metadata' => [
                'nullable',
                'array',
            ],
            
            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }
    
    public function attributes(): array
    {
        return [
            'exchange_id' => __('pair.fields.exchange'),
            'base_asset' => __('pair.fields.base_asset'),
            'quote_asset' => __('pair.fields.quote_asset'),
            'symbol' => __('pair.fields.symbol'),
            'status' => __('pair.fields.status'),
            'price_precision' => __('pair.fields.price_precision'),
            'quantity_precision' => __('pair.fields.quantity_precision'),
            'min_qty' => __('pair.fields.min_qty'),
            'max_qty' => __('pair.fields.max_qty'),
            'tick_size' => __('pair.fields.tick_size'),
            'step_size' => __('pair.fields.step_size'),
            'min_notional' => __('pair.fields.min_notional'),
            'is_active' => __('pair.fields.is_active'),
        ];
    }
}