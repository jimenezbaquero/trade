<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ExchangeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
            'slug' => ['required', 'string', 'unique:exchanges,slug'],
            
            'api_url' => ['nullable', 'url'],
            'testnet_api_url' => ['nullable', 'url'],
            
            'websocket_url' => ['nullable', 'string'],
            'testnet_websocket_url' => ['nullable', 'string'],
            
            'rate_limit' => ['nullable', 'integer'],
            
            'is_active' => ['boolean'],
        ];
    }
}
