<?php

namespace App\Http\Requests;

use App\Support\RecommendationQuestions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BarSettingsRequest extends FormRequest
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
            'logo' => ['nullable', 'image', 'max:2048'],
            'brand_background_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'brand_primary_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'welcome_message' => ['nullable', 'string', 'max:500'],
            'qr_enabled' => ['boolean'],
            'offers_beer' => ['boolean'],
            'offers_wine' => ['boolean'],
            'recommendation_questions' => ['nullable', 'array', 'min:3', 'max:10'],
            'recommendation_questions.*' => ['string', Rule::in(RecommendationQuestions::ids('beer'))],
            'recommendation_questions_wine' => ['nullable', 'array', 'min:3', 'max:10'],
            'recommendation_questions_wine.*' => ['string', Rule::in(RecommendationQuestions::ids('wine'))],
        ];
    }
}
