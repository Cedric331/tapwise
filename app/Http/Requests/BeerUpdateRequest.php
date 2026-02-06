<?php

namespace App\Http\Requests;

use App\Enums\BeerColor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BeerUpdateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'brewery' => ['nullable', 'string', 'max:255'],
            'style' => ['required', 'string', 'max:255'],
            'color' => ['required', Rule::in(array_map(fn (BeerColor $color) => $color->value, BeerColor::cases()))],
            'abv_x10' => ['required', 'integer', 'min:0', 'max:200'],
            'ibu' => ['nullable', 'integer', 'min:0', 'max:120'],
            'description' => ['nullable', 'string'],
            'is_on_tap' => ['boolean'],
            'is_available' => ['boolean'],
            'price' => ['nullable', 'integer', 'min:0'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ];
    }
}
