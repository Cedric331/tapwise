<?php

namespace App\Http\Controllers;

use App\Enums\BeerColor;
use App\Enums\WineColor;
use App\Models\BarScanEvent;
use App\Models\Bar;
use App\Models\RecommendationEvent;
use App\Services\BeerRecommendationService;
use App\Services\WineRecommendationService;
use App\Support\RecommendationQuestions;
use App\Support\WineFoodPairings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PublicRecommendationController extends Controller
{
    public function __construct(
        protected BeerRecommendationService $recommendationService,
        protected WineRecommendationService $wineRecommendationService
    ) {}

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
        BarScanEvent::create(['bar_id' => $bar->id]);

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
        $selectedBeerQuestions = RecommendationQuestions::normalizeSelected($bar->recommendation_questions, 'beer');

        $availableWineColors = DB::table('wines')
            ->where('bar_id', $bar->id)
            ->distinct()
            ->pluck('color')
            ->filter()
            ->values()
            ->all();
        $wineColorOptions = array_intersect_key(WineColor::options(), array_flip($availableWineColors));
        $wineAromaOptions = DB::table('wine_tags')
            ->join('wine_wine_tag', 'wine_tags.id', '=', 'wine_wine_tag.wine_tag_id')
            ->join('wines', 'wines.id', '=', 'wine_wine_tag.wine_id')
            ->where('wines.bar_id', $bar->id)
            ->distinct()
            ->orderBy('wine_tags.name')
            ->get(['wine_tags.slug as value', 'wine_tags.name as label'])
            ->values()
            ->all();
        $wineFoodPairings = DB::table('wines')
            ->where('bar_id', $bar->id)
            ->whereNotNull('food_pairings')
            ->pluck('food_pairings')
            ->all();
        $availableFoodPairings = [];
        foreach ($wineFoodPairings as $pairings) {
            $decoded = is_string($pairings) ? json_decode($pairings, true) : $pairings;
            if (is_array($decoded)) {
                foreach ($decoded as $pairing) {
                    if (is_string($pairing)) {
                        $availableFoodPairings[] = $pairing;
                    }
                }
            }
        }
        $availableFoodPairings = array_values(array_unique($availableFoodPairings));
        $foodPairingOptions = array_values(array_filter(
            WineFoodPairings::options(),
            fn (array $option) => in_array($option['id'], $availableFoodPairings, true)
        ));
        $grapeOptions = DB::table('wines')
            ->where('bar_id', $bar->id)
            ->whereNotNull('grape')
            ->where('grape', '!=', '')
            ->distinct()
            ->orderBy('grape')
            ->pluck('grape')
            ->values()
            ->all();
        $regionOptions = DB::table('wines')
            ->where('bar_id', $bar->id)
            ->whereNotNull('region')
            ->where('region', '!=', '')
            ->distinct()
            ->orderBy('region')
            ->pluck('region')
            ->values()
            ->all();
        $maxWinePrice = DB::table('wines')
            ->where('bar_id', $bar->id)
            ->whereNotNull('price')
            ->max('price');
        $selectedWineQuestions = RecommendationQuestions::normalizeSelected(
            $bar->recommendation_questions_wine,
            'wine'
        );

        $hasBeerData = DB::table('beers')->where('bar_id', $bar->id)->exists();
        $hasWineData = DB::table('wines')->where('bar_id', $bar->id)->exists();
        $availableDrinkTypes = [];
        if ($bar->offers_beer && $hasBeerData) {
            $availableDrinkTypes[] = 'beer';
        }
        if ($bar->offers_wine && $hasWineData) {
            $availableDrinkTypes[] = 'wine';
        }
        if ($availableDrinkTypes === []) {
            $availableDrinkTypes[] = 'beer';
        }

        return Inertia::render('Public/Recommendation', [
            'bar' => $bar,
            'colorOptions' => $colorOptions,
            'aromaOptions' => $aromaOptions,
            'styleOptions' => $styleOptions,
            'breweryOptions' => $breweryOptions,
            'maxPrice' => $maxPrice,
            'questionOptionsBeer' => RecommendationQuestions::all('beer'),
            'questionOptionsWine' => RecommendationQuestions::all('wine'),
            'selectedQuestionsBeer' => $selectedBeerQuestions,
            'selectedQuestionsWine' => $selectedWineQuestions,
            'wineColorOptions' => $wineColorOptions,
            'wineAromaOptions' => $wineAromaOptions,
            'grapeOptions' => $grapeOptions,
            'regionOptions' => $regionOptions,
            'maxWinePrice' => $maxWinePrice,
            'foodPairingOptions' => $foodPairingOptions,
            'availableDrinkTypes' => $availableDrinkTypes,
        ]);
    }

    /**
     * Display the public menu page for a bar.
     */
    public function menu(string $slug): Response
    {
        $bar = Bar::where('slug', $slug)->firstOrFail();

        if (! $bar->qr_enabled || ! $bar->hasActiveAccess()) {
            return Inertia::render('Public/Disabled', [
                'bar' => $bar,
            ]);
        }

        $beers = $bar->availableBeers()
            ->with('tags')
            ->orderBy('name')
            ->get();
        $wines = $bar->availableWines()
            ->with('tags')
            ->orderBy('name')
            ->get();

        $popularity = [
            'beer' => [],
            'wine' => [],
        ];
        RecommendationEvent::query()
            ->where('bar_id', $bar->id)
            ->where('created_at', '>=', now()->subDays(30)->startOfDay())
            ->get(['drink_type', 'item_ids'])
            ->each(function (RecommendationEvent $event) use (&$popularity) {
                $type = $event->drink_type === 'wine' ? 'wine' : 'beer';
                $items = is_array($event->item_ids) ? $event->item_ids : [];
                foreach ($items as $id) {
                    if (! is_int($id)) {
                        continue;
                    }
                    $popularity[$type][$id] = ($popularity[$type][$id] ?? 0) + 1;
                }
            });

        $beers = $beers->map(function ($beer) use ($popularity) {
            $beer->popularity = $popularity['beer'][$beer->id] ?? 0;
            return $beer;
        });
        $wines = $wines->map(function ($wine) use ($popularity) {
            $wine->popularity = $popularity['wine'][$wine->id] ?? 0;
            return $wine;
        });

        return Inertia::render('Public/Menu', [
            'bar' => $bar,
            'beers' => $beers,
            'wines' => $wines,
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

        $availableDrinkTypes = [];
        $hasBeerData = DB::table('beers')->where('bar_id', $bar->id)->exists();
        $hasWineData = DB::table('wines')->where('bar_id', $bar->id)->exists();
        if ($bar->offers_beer && $hasBeerData) {
            $availableDrinkTypes[] = 'beer';
        }
        if ($bar->offers_wine && $hasWineData) {
            $availableDrinkTypes[] = 'wine';
        }
        if ($availableDrinkTypes === []) {
            $availableDrinkTypes[] = 'beer';
        }

        $rules = [
            'drink_type' => ['required', Rule::in($availableDrinkTypes)],
        ];

        $drinkType = $request->input('drink_type');

        if ($drinkType === 'wine') {
            $availableAromas = DB::table('wine_tags')
                ->join('wine_wine_tag', 'wine_tags.id', '=', 'wine_wine_tag.wine_tag_id')
                ->join('wines', 'wines.id', '=', 'wine_wine_tag.wine_id')
                ->where('wines.bar_id', $bar->id)
                ->distinct()
                ->pluck('wine_tags.slug')
                ->all();
            $availableGrapes = DB::table('wines')
                ->where('bar_id', $bar->id)
                ->whereNotNull('grape')
                ->where('grape', '!=', '')
                ->distinct()
                ->pluck('grape')
                ->all();
            $availableRegions = DB::table('wines')
                ->where('bar_id', $bar->id)
                ->whereNotNull('region')
                ->where('region', '!=', '')
                ->distinct()
                ->pluck('region')
                ->all();
            $maxPrice = DB::table('wines')
                ->where('bar_id', $bar->id)
                ->whereNotNull('price')
                ->max('price');
            $maxPriceEuros = $maxPrice ? ceil($maxPrice / 100) : 12;
            $maxPriceEuros = max($maxPriceEuros, 5);
            $selectedQuestions = RecommendationQuestions::normalizeSelected(
                $bar->recommendation_questions_wine,
                'wine'
            );

            $availableWineColors = DB::table('wines')
                ->where('bar_id', $bar->id)
                ->distinct()
                ->pluck('color')
                ->filter()
                ->values()
                ->all();

            if (in_array('color', $selectedQuestions, true) && $availableWineColors !== []) {
                $rules['color'] = ['required', 'array', 'min:1'];
                $rules['color.*'] = [Rule::in(array_map(fn (WineColor $color) => $color->value, WineColor::cases()))];
            }

            if (in_array('aromas', $selectedQuestions, true) && $availableAromas !== []) {
                $rules['aromas'] = ['required', 'array'];
                $rules['aromas.*'] = [Rule::in($availableAromas)];
            }

            $wineFoodPairings = DB::table('wines')
                ->where('bar_id', $bar->id)
                ->whereNotNull('food_pairings')
                ->pluck('food_pairings')
                ->all();
            $availableFoodPairings = [];
            foreach ($wineFoodPairings as $pairings) {
                $decoded = is_string($pairings) ? json_decode($pairings, true) : $pairings;
                if (is_array($decoded)) {
                    foreach ($decoded as $pairing) {
                        if (is_string($pairing)) {
                            $availableFoodPairings[] = $pairing;
                        }
                    }
                }
            }
            $availableFoodPairings = array_values(array_unique($availableFoodPairings));

            if (in_array('food', $selectedQuestions, true) && $availableFoodPairings !== []) {
                $rules['food_pairings'] = ['required', 'array', 'min:1'];
                $rules['food_pairings.*'] = [Rule::in(WineFoodPairings::ids())];
            }

            if (in_array('grape', $selectedQuestions, true) && $availableGrapes !== []) {
                $rules['grape'] = ['required', Rule::in(array_merge(['any'], $availableGrapes))];
            }

            if (in_array('region', $selectedQuestions, true) && $availableRegions !== []) {
                $rules['region'] = ['required', Rule::in(array_merge(['any'], $availableRegions))];
            }

            if (in_array('max_abv', $selectedQuestions, true)) {
                $rules['max_abv'] = ['required', 'numeric', 'min:0', 'max:25'];
            }

            if (in_array('max_price', $selectedQuestions, true) && $maxPriceEuros > 0) {
                $rules['max_price'] = ['required', 'numeric', 'min:0', 'max:'.$maxPriceEuros];
            }

            $validated = $request->validate($rules);
            $validated['_active_questions'] = RecommendationQuestions::resolveActiveQuestions(
                $selectedQuestions,
                $validated
            );

            $recommendations = $this->wineRecommendationService->getRecommendations($bar, $validated);
            RecommendationEvent::create([
                'bar_id' => $bar->id,
                'drink_type' => 'wine',
                'item_ids' => $recommendations->pluck('id')->all(),
            ]);

            $recommendationsWithExplanations = $recommendations->map(function ($wine) use ($validated) {
                return [
                    'wine' => $wine,
                    'explanation' => $this->wineRecommendationService->getExplanation($wine, $validated),
                ];
            });
            $preferencesForView = $validated;
            unset($preferencesForView['_active_questions']);

            return Inertia::render('Public/Results', [
                'bar' => $bar,
                'drinkType' => 'wine',
                'recommendations' => $recommendationsWithExplanations,
                'preferences' => $preferencesForView,
            ]);
        }

        $availableAromas = DB::table('tags')
            ->join('beer_tag', 'tags.id', '=', 'beer_tag.tag_id')
            ->join('beers', 'beers.id', '=', 'beer_tag.beer_id')
            ->where('beers.bar_id', $bar->id)
            ->distinct()
            ->pluck('tags.slug')
            ->all();
        $availableColors = DB::table('beers')
            ->where('bar_id', $bar->id)
            ->distinct()
            ->pluck('color')
            ->filter()
            ->values()
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
        $selectedQuestions = RecommendationQuestions::normalizeSelected($bar->recommendation_questions, 'beer');

        if (in_array('bitterness', $selectedQuestions, true)) {
            $rules['bitterness'] = ['required', 'in:faible,moyenne,forte'];
        }

        if (in_array('color', $selectedQuestions, true) && $availableColors !== []) {
            $rules['color'] = ['required', 'array', 'min:1'];
            $rules['color.*'] = [Rule::in(array_map(fn (BeerColor $color) => $color->value, BeerColor::cases()))];
        }

        if (in_array('aromas', $selectedQuestions, true) && $availableAromas !== []) {
            $rules['aromas'] = ['required', 'array'];
            $rules['aromas.*'] = [Rule::in($availableAromas)];
        }

        if (in_array('max_abv', $selectedQuestions, true)) {
            $rules['max_abv'] = ['required', 'numeric', 'min:0', 'max:15'];
        }

        if (in_array('format', $selectedQuestions, true)) {
            $rules['format'] = ['required', 'in:pression,bouteille'];
        }

        if (in_array('style', $selectedQuestions, true) && $availableStyles !== []) {
            $rules['style'] = ['required', Rule::in(array_merge(['any'], $availableStyles))];
        }

        if (in_array('brewery', $selectedQuestions, true) && $availableBreweries !== []) {
            $rules['brewery'] = ['required', Rule::in(array_merge(['any'], $availableBreweries))];
        }

        if (in_array('max_price', $selectedQuestions, true) && $maxPriceEuros > 0) {
            $rules['max_price'] = ['required', 'numeric', 'min:0', 'max:'.$maxPriceEuros];
        }

        $validated = $request->validate($rules);
        $validated['_active_questions'] = RecommendationQuestions::resolveActiveQuestions(
            $selectedQuestions,
            $validated
        );

        $recommendations = $this->recommendationService->getRecommendations($bar, $validated);
        RecommendationEvent::create([
            'bar_id' => $bar->id,
            'drink_type' => 'beer',
            'item_ids' => $recommendations->pluck('id')->all(),
        ]);

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
            'drinkType' => 'beer',
            'recommendations' => $recommendationsWithExplanations,
            'preferences' => $preferencesForView,
        ]);
    }
}
