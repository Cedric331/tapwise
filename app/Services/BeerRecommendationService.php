<?php

namespace App\Services;

use App\Enums\BeerColor;
use App\Models\Bar;
use App\Models\Beer;
use App\Support\RecommendationQuestions;
use Illuminate\Support\Collection;

class BeerRecommendationService
{
    /**
     * Get beer recommendations based on user preferences.
     *
     * @param  array<string, mixed>  $preferences
     * @return Collection<int, Beer>
     */
    public function getRecommendations(Bar $bar, array $preferences): Collection
    {
        // Get available beers
        $beers = $bar->availableBeers()->with('tags')->get();

        if ($beers->isEmpty()) {
            return collect();
        }

        // Score each beer
        $scoredBeers = $beers->map(function (Beer $beer) use ($preferences) {
            return [
                'beer' => $beer,
                'score' => $this->calculateScore($beer, $preferences),
            ];
        });

        // Sort by score descending and return top 3
        return $scoredBeers
            ->sortByDesc('score')
            ->take(3)
            ->pluck('beer');
    }

    /**
     * Calculate a score for a beer based on preferences.
     */
    protected function calculateScore(Beer $beer, array $preferences): float
    {
        $score = 0.0;
        $activeQuestions = $preferences['_active_questions'] ?? RecommendationQuestions::DEFAULT;
        $weights = $this->normalizeWeights($activeQuestions);

        // Tag matching (30% weight)
        if (($weights['aromas'] ?? 0) > 0 && isset($preferences['aromas']) && is_array($preferences['aromas'])) {
            $beerTagSlugs = $beer->tags->pluck('slug')->toArray();
            $matchingTags = count(array_intersect($preferences['aromas'], $beerTagSlugs));
            $totalTags = count($preferences['aromas']);
            
            if ($totalTags > 0) {
                $score += ($matchingTags / $totalTags) * $weights['aromas'];
            }
        }

        // ABV matching (25% weight)
        if (($weights['max_abv'] ?? 0) > 0 && isset($preferences['max_abv'])) {
            $maxAbv = (float) $preferences['max_abv'];
            $beerAbv = $beer->abv;
            
            if ($beerAbv <= $maxAbv) {
                // Prefer beers closer to max ABV
                $score += (($beerAbv / max($maxAbv, 1)) * $weights['max_abv']);
            } else {
                // Penalize beers over max ABV
                $score -= $weights['max_abv'] * 0.8;
            }
        }

        // Bitterness matching (20% weight)
        if (($weights['bitterness'] ?? 0) > 0 && isset($preferences['bitterness']) && $beer->ibu !== null) {
            $bitterness = $preferences['bitterness'];
            $ibu = $beer->ibu;
            
            if ($bitterness === 'faible' && $ibu <= 25) {
                $score += $weights['bitterness'];
            } elseif ($bitterness === 'moyenne' && $ibu > 25 && $ibu <= 50) {
                $score += $weights['bitterness'];
            } elseif ($bitterness === 'forte' && $ibu > 50) {
                $score += $weights['bitterness'];
            }
        }

        // Format preference (10% weight)
        if (($weights['format'] ?? 0) > 0 && isset($preferences['format'])) {
            $format = $preferences['format'];
            
            if ($format === 'pression' && $beer->is_on_tap) {
                $score += $weights['format'];
            } elseif ($format === 'bouteille' && !$beer->is_on_tap) {
                $score += $weights['format'];
            }
        }

        // Color preference (15% weight)
        if (($weights['color'] ?? 0) > 0 && isset($preferences['color']) && is_array($preferences['color'])) {
            $beerColor = $beer->color instanceof BeerColor ? $beer->color->value : (string) $beer->color;
            if (in_array($beerColor, $preferences['color'], true)) {
                $score += $weights['color'];
            }
        }

        // Style preference
        if (($weights['style'] ?? 0) > 0 && ! empty($preferences['style']) && $preferences['style'] !== 'any') {
            if ($beer->style && strcasecmp($beer->style, (string) $preferences['style']) === 0) {
                $score += $weights['style'];
            }
        }

        // Brewery preference
        if (($weights['brewery'] ?? 0) > 0 && ! empty($preferences['brewery']) && $preferences['brewery'] !== 'any') {
            if ($beer->brewery && strcasecmp($beer->brewery, (string) $preferences['brewery']) === 0) {
                $score += $weights['brewery'];
            }
        }

        // Price preference
        if (($weights['max_price'] ?? 0) > 0 && isset($preferences['max_price']) && $beer->price !== null) {
            $maxPrice = (float) $preferences['max_price'];
            $beerPrice = $beer->price / 100;

            if ($beerPrice <= $maxPrice) {
                $score += $weights['max_price'];
            } else {
                $score -= $weights['max_price'] * 0.8;
            }
        }

        return max(0, $score);
    }

    /**
     * Get explanation text for a recommendation.
     */
    public function getExplanation(Beer $beer, array $preferences): string
    {
        $reasons = [];
        $activeQuestions = $preferences['_active_questions'] ?? RecommendationQuestions::DEFAULT;

        // Check tag matches
        if (in_array('aromas', $activeQuestions, true) && isset($preferences['aromas']) && is_array($preferences['aromas'])) {
            $beerTagSlugs = $beer->tags->pluck('slug')->toArray();
            $matchingTags = array_intersect($preferences['aromas'], $beerTagSlugs);
            
            if (!empty($matchingTags)) {
                $tagNames = $beer->tags->whereIn('slug', $matchingTags)->pluck('name')->join(', ');
                $reasons[] = "Correspond à vos arômes préférés : {$tagNames}";
            }
        }

        // Check ABV
        if (in_array('max_abv', $activeQuestions, true) && isset($preferences['max_abv'])) {
            $maxAbv = (float) $preferences['max_abv'];
            if ($beer->abv <= $maxAbv) {
                $reasons[] = "Taux d'alcool adapté ({$beer->abv}%)";
            }
        }

        // Check bitterness
        if (in_array('bitterness', $activeQuestions, true) && isset($preferences['bitterness']) && $beer->ibu !== null) {
            $bitterness = $preferences['bitterness'];
            $ibu = $beer->ibu;
            
            if (($bitterness === 'faible' && $ibu <= 25) ||
                ($bitterness === 'moyenne' && $ibu > 25 && $ibu <= 50) ||
                ($bitterness === 'forte' && $ibu > 50)) {
                $reasons[] = "Amertume correspondant à vos préférences";
            }
        }

        if (in_array('color', $activeQuestions, true) && isset($preferences['color']) && is_array($preferences['color'])) {
            $colorValue = $beer->color instanceof BeerColor ? $beer->color->value : (string) $beer->color;
            if (in_array($colorValue, $preferences['color'], true)) {
                $colorLabel = $beer->color instanceof BeerColor
                    ? $beer->color->label()
                    : (BeerColor::tryFrom($colorValue)?->label() ?? ucfirst($colorValue));
                $reasons[] = "Couleur correspondant à vos préférences ({$colorLabel})";
            }
        }

        if (in_array('format', $activeQuestions, true) && isset($preferences['format'])) {
            if ($preferences['format'] === 'pression' && $beer->is_on_tap) {
                $reasons[] = "Disponible à la pression";
            } elseif ($preferences['format'] === 'bouteille' && !$beer->is_on_tap) {
                $reasons[] = "Disponible en bouteille";
            }
        }

        if (in_array('style', $activeQuestions, true) && ! empty($preferences['style']) && $preferences['style'] !== 'any') {
            if ($beer->style && strcasecmp($beer->style, (string) $preferences['style']) === 0) {
                $reasons[] = "Style correspondant à vos préférences ({$beer->style})";
            }
        }

        if (in_array('brewery', $activeQuestions, true) && ! empty($preferences['brewery']) && $preferences['brewery'] !== 'any') {
            if ($beer->brewery && strcasecmp($beer->brewery, (string) $preferences['brewery']) === 0) {
                $reasons[] = "Brasserie correspondant à vos préférences ({$beer->brewery})";
            }
        }

        if (in_array('max_price', $activeQuestions, true) && isset($preferences['max_price']) && $beer->price !== null) {
            $maxPrice = (float) $preferences['max_price'];
            $beerPrice = $beer->price / 100;
            if ($beerPrice <= $maxPrice) {
                $reasons[] = "Prix adapté à votre budget (≤ {$maxPrice}€)";
            }
        }

        if (empty($reasons)) {
            return "Sélection basée sur le profil de la bière";
        }

        return implode('. ', $reasons) . '.';
    }

    /**
     * @param  array<int, string>  $activeQuestions
     * @return array<string, float>
     */
    protected function normalizeWeights(array $activeQuestions): array
    {
        $baseWeights = RecommendationQuestions::weights();
        $activeWeights = array_intersect_key($baseWeights, array_flip($activeQuestions));

        $total = array_sum($activeWeights);
        if ($total <= 0) {
            return [];
        }

        return array_map(fn (float $weight) => ($weight / $total) * 100, $activeWeights);
    }
}

