<?php

namespace App\Http\Requests;

use App\Enums\WineColor;
use App\Support\WineFoodPairings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WineUpdateRequest extends FormRequest
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
            'color' => ['required', Rule::in(array_map(fn (WineColor $color) => $color->value, WineColor::cases()))],
            'grape' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'food_pairings' => ['nullable', 'array'],
            'food_pairings.*' => ['string', Rule::in(WineFoodPairings::ids())],
            'abv_x10' => ['required', 'integer', 'min:0', 'max:250'],
            'description' => ['nullable', 'string'],
            'is_available' => ['boolean'],
            'price' => ['nullable', 'integer', 'min:0'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:wine_tags,id'],
        ];
    }
}
