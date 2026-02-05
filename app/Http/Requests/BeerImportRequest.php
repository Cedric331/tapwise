<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BeerImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
            'mapping' => ['nullable', 'array'],
            'mapping.name' => ['nullable', 'string'],
            'mapping.brewery' => ['nullable', 'string'],
            'mapping.style' => ['nullable', 'string'],
            'mapping.color' => ['nullable', 'string'],
            'mapping.abv' => ['nullable', 'string'],
            'mapping.abv_x10' => ['nullable', 'string'],
            'mapping.ibu' => ['nullable', 'string'],
            'mapping.description' => ['nullable', 'string'],
            'mapping.is_on_tap' => ['nullable', 'string'],
            'mapping.is_available' => ['nullable', 'string'],
            'mapping.price' => ['nullable', 'string'],
        ];
    }
}

