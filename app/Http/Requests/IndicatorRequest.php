<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndicatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            
            'handler' => ['required', 'string', 'max:50'],
            
            'config' => [
                'nullable',
                'array',
            ],
        ];
    }
    
    public function messages(): array
    {
        return [
            'config.required' => 'La configuración es obligatoria.',
            'config.string' => 'La configuración debe ser un JSON válido en formato texto.',
        ];
    }
    
    protected function prepareForValidation(): void
    {
        if ($this->has('config')) {
            
            $decoded = json_decode($this->config, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge([
                    'config' => $decoded,
                ]);
            }
        }
    }
    
    protected function passedValidation(): void
    {
        // fallback de seguridad extra (por si llega string raro)
        if (is_string($this->config)) {
            $this->merge([
                'config' => json_decode($this->config, true) ?? [],
            ]);
        }
    }
}