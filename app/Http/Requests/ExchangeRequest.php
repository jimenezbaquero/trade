<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExchangeRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'slug' => ['required', 'string',  Rule::unique('exchanges', 'slug')
                ->ignore($this->route('exchange'))],
            'api_url' => ['nullable', 'url'],
            'testnet_api_url' => ['nullable', 'url'],
            'websocket_url' => ['nullable', 'string'],
            'testnet_websocket_url' => ['nullable', 'string'],
            'rate_limit' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ];
    }
    
    public function attributes(): array
    {
        return [
            'name' => mb_strtolower(__('exchange.fields.name')),
            'slug' => mb_strtolower(__('exchange.fields.slug')),
            'api_url' => mb_strtolower(__('exchange.fields.api_url')),
            'testnet_api_url' => mb_strtolower(__('exchange.fields.testnet_api_url')),
            'websocket_url' => mb_strtolower(__('exchange.fields.websocket_url')),
            'testnet_websocket_url' => mb_strtolower(__('exchange.fields.testnet_websocket_url')),
            'rate_limit' => mb_strtolower(__('exchange.fields.rate_limit')),
            'is_active' => mb_strtolower(__('exchange.fields.is_active')),
        ];
    }
}
