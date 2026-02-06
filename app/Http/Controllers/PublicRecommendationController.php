<?php

namespace App\Http\Controllers;

use App\Enums\BeerColor;
use App\Models\Bar;
use App\Services\BeerRecommendationService;
use App\Support\RecommendationQuestions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PublicRecommendationController extends Controller
{
    public function __construct(
        protected BeerRecommendationService $recommendationService
    ) {
    }

    /**
     * Display the public recommendation page for a bar.
     */
    public function show(string $slug): Response
    {
        $bar = Bar::where('slug', $slug)->firstOrFail();

        // Check if QR is enabled
        if (! $bar->qr_enabled || ! $bar->hasActiveAccess()) {
            return Inertia::render('Public/Disabled', [
                'bar' => $bar,
            ]);
        }

        $bar->increment('count_scans');

        $availableColors = DB::table('beers')
            ->where('bar_id', $bar->id)
            ->distinct()
            ->pluck('color')
            ->filter()
            ->values()
            ->all();

        $colorOptions = array_intersect_key(BeerColor::options(), array_flip($availableColors));
        $aromaOptions = DB::table('tags')
            ->join('beer_tag', 'tags.id', '=', 'beer_tag.tag_id')
            ->join('beers', 'beers.id', '=', 'beer_tag.beer_id')
            ->where('beers.bar_id', $bar->id)
            ->distinct()
            ->orderBy('tags.name')
            ->get(['tags.slug as value', 'tags.name as label'])
            ->values()
            ->all();
        $styleOptions = DB::table('beers')
            ->where('bar_id', $bar->id)
            ->whereNotNull('style')
            ->where('style', '!=', '')
            ->distinct()
            ->orderBy('style')
            ->pluck('style')
            ->values()
            ->all();
        $breweryOptions = DB::table('beers')
            ->where('bar_id', $bar->id)
            ->whereNotNull('brewery')
            ->where('brewery', '!=', '')
            ->distinct()
            ->orderBy('brewery')
            ->pluck('brewery')
            ->values()
            ->all();
        $maxPrice = DB::table('beers')
            ->where('bar_id', $bar->id)
            ->whereNotNull('price')
            ->max('price');
        $selectedQuestions = RecommendationQuestions::normalizeSelected($bar->recommendation_questions);

        return Inertia::render('Public/Recommendation', [
            'bar' => $bar,
            'colorOptions' => $colorOptions,
            'aromaOptions' => $aromaOptions,
            'styleOptions' => $styleOptions,
            'breweryOptions' => $breweryOptions,
            'maxPrice' => $maxPrice,
            'questionOptions' => RecommendationQuestions::all(),
            'selectedQuestions' => $selectedQuestions,
        ]);
    }

    /**
     * Process the recommendation form and show results.
     */
    public function recommend(Request $request, string $slug): Response
    {
        $bar = Bar::where('slug', $slug)->firstOrFail();

        if (! $bar->qr_enabled || ! $bar->hasActiveAccess()) {
            return Inertia::render('Public/Disabled', [
                'bar' => $bar,
            ]);
        }

        $availableAromas = DB::table('tags')
            ->join('beer_tag', 'tags.id', '=', 'beer_tag.tag_id')
            ->join('beers', 'beers.id', '=', 'beer_tag.beer_id')
            ->where('beers.bar_id', $bar->id)
            ->distinct()
            ->pluck('tags.slug')
            ->all();
        $availableStyles = DB::table('beers')
            ->where('bar_id', $bar->id)
            ->whereNotNull('style')
            ->where('style', '!=', '')
            ->distinct()
            ->pluck('style')
            ->all();
        $availableBreweries = DB::table('beers')
            ->where('bar_id', $bar->id)
            ->whereNotNull('brewery')
            ->where('brewery', '!=', '')
            ->distinct()
            ->pluck('brewery')
            ->all();
        $maxPrice = DB::table('beers')
            ->where('bar_id', $bar->id)
            ->whereNotNull('price')
            ->max('price');
        $maxPriceEuros = $maxPrice ? ceil($maxPrice / 100) : 12;
        $maxPriceEuros = max($maxPriceEuros, 5);
        $selectedQuestions = RecommendationQuestions::normalizeSelected($bar->recommendation_questions);

        $rules = [];

        if (in_array('bitterness', $selectedQuestions, true)) {
            $rules['bitterness'] = ['required', 'in:faible,moyenne,forte'];
        }

        if (in_array('color', $selectedQuestions, true)) {
            $rules['color'] = ['required', 'array', 'min:1'];
            $rules['color.*'] = [Rule::in(array_map(fn (BeerColor $color) => $color->value, BeerColor::cases()))];
        }

        if (in_array('aromas', $selectedQuestions, true)) {
            $rules['aromas'] = ['required', 'array'];
            $rules['aromas.*'] = [Rule::in($availableAromas)];
        }

        if (in_array('max_abv', $selectedQuestions, true)) {
            $rules['max_abv'] = ['required', 'numeric', 'min:0', 'max:15'];
        }

        if (in_array('format', $selectedQuestions, true)) {
            $rules['format'] = ['required', 'in:pression,bouteille'];
        }

        if (in_array('style', $selectedQuestions, true)) {
            $rules['style'] = ['required', Rule::in(array_merge(['any'], $availableStyles))];
        }

        if (in_array('brewery', $selectedQuestions, true)) {
            $rules['brewery'] = ['required', Rule::in(array_merge(['any'], $availableBreweries))];
        }

        if (in_array('max_price', $selectedQuestions, true)) {
            $rules['max_price'] = ['required', 'numeric', 'min:0', 'max:' . $maxPriceEuros];
        }

        $validated = $request->validate($rules);
        $validated['_active_questions'] = RecommendationQuestions::resolveActiveQuestions(
            $selectedQuestions,
            $validated
        );

        $recommendations = $this->recommendationService->getRecommendations($bar, $validated);

        $recommendationsWithExplanations = $recommendations->map(function ($beer) use ($validated) {
            return [
                'beer' => $beer,
                'explanation' => $this->recommendationService->getExplanation($beer, $validated),
            ];
        });
        $preferencesForView = $validated;
        unset($preferencesForView['_active_questions']);

        return Inertia::render('Public/Results', [
            'bar' => $bar,
            'recommendations' => $recommendationsWithExplanations,
            'preferences' => $preferencesForView,
        ]);
    }
}

