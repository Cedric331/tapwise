<?php

namespace App\Http\Controllers;

use App\Enums\BeerColor;
use App\Models\Bar;
use App\Services\BeerRecommendationService;
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
        if (!$bar->qr_enabled) {
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

        return Inertia::render('Public/Recommendation', [
            'bar' => $bar,
            'colorOptions' => $colorOptions,
            'aromaOptions' => $aromaOptions,
        ]);
    }

    /**
     * Process the recommendation form and show results.
     */
    public function recommend(Request $request, string $slug): Response
    {
        $bar = Bar::where('slug', $slug)->firstOrFail();

        if (!$bar->qr_enabled) {
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

        $validated = $request->validate([
            'bitterness' => ['required', 'in:faible,moyenne,forte'],
            'color' => ['required', 'array', 'min:1'],
            'color.*' => [Rule::in(array_map(fn (BeerColor $color) => $color->value, BeerColor::cases()))],
            'aromas' => ['required', 'array'],
            'aromas.*' => [Rule::in($availableAromas)],
            'max_abv' => ['required', 'numeric', 'min:0', 'max:15'],
            'format' => ['required', 'in:pression,bouteille'],
        ]);

        $recommendations = $this->recommendationService->getRecommendations($bar, $validated);

        $recommendationsWithExplanations = $recommendations->map(function ($beer) use ($validated) {
            return [
                'beer' => $beer,
                'explanation' => $this->recommendationService->getExplanation($beer, $validated),
            ];
        });

        return Inertia::render('Public/Results', [
            'bar' => $bar,
            'recommendations' => $recommendationsWithExplanations,
            'preferences' => $validated,
        ]);
    }
}

