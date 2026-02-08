<?php

namespace App\Services;

use App\Enums\WineColor;
use App\Models\Bar;
use App\Models\Wine;
use App\Support\RecommendationQuestions;
use Illuminate\Support\Collection;

class WineRecommendationService
{
    private const TOLERANCE_RATIO = 0.10;
    private const ABV_TOLERANCE_ABS = 0.5;
    private const PRICE_TOLERANCE_ABS = 0.5;
    private const NEAR_WEIGHT = 0.5;
    private const QUALITY_THRESHOLD = 0.9;

    /**
     * Get wine recommendations based on user preferences.
     *
     * @param  array<string, mixed>  $preferences
     * @return Collection<int, Wine>
     */
    public function getRecommendations(Bar $bar, array $preferences): Collection
    {
        $wines = $bar->availableWines()->with('tags')->get();

        if ($wines->isEmpty()) {
            return collect();
        }

        $wines = $wines->filter(fn (Wine $wine) => $this->passesHardLimits($wine, $preferences));

        if ($wines->isEmpty()) {
            return collect();
        }

        $wines = $wines->filter(fn (Wine $wine) => $this->passesOverallThreshold($wine, $preferences));

        if ($wines->isEmpty()) {
            return collect();
        }

        $scoredWines = $wines->map(function (Wine $wine) use ($preferences) {
            return [
                'wine' => $wine,
                'score' => $this->calculateScore($wine, $preferences),
            ];
        });

        return $scoredWines
            ->sortByDesc('score')
            ->take(3)
            ->pluck('wine');
    }

    /**
     * Calculate a score for a wine based on preferences.
     */
    protected function calculateScore(Wine $wine, array $preferences): float
    {
        $score = 0.0;
        $activeQuestions = $preferences['_active_questions'] ?? RecommendationQuestions::DEFAULT_WINE;
        $weights = $this->normalizeWeights($activeQuestions);

        if (($weights['aromas'] ?? 0) > 0 && isset($preferences['aromas']) && is_array($preferences['aromas'])) {
            $wineTagSlugs = $wine->tags->pluck('slug')->toArray();
            $matchingTags = count(array_intersect($preferences['aromas'], $wineTagSlugs));
            $totalTags = count($preferences['aromas']);

            if ($totalTags > 0) {
                $score += ($matchingTags / $totalTags) * $weights['aromas'];
            }
        }

        if (($weights['food'] ?? 0) > 0 && isset($preferences['food_pairings']) && is_array($preferences['food_pairings'])) {
            $winePairings = is_array($wine->food_pairings) ? $wine->food_pairings : [];
            $matching = count(array_intersect($preferences['food_pairings'], $winePairings));
            $total = count($preferences['food_pairings']);

            if ($total > 0) {
                $score += ($matching / $total) * $weights['food'];
            }
        }

        if (($weights['max_abv'] ?? 0) > 0 && isset($preferences['max_abv'])) {
            $maxAbv = (float) $preferences['max_abv'];
            $wineAbv = $wine->abv;

            if ($wineAbv <= $maxAbv) {
                $score += (($wineAbv / max($maxAbv, 1)) * $weights['max_abv']);
            } else {
                $score -= $weights['max_abv'] * 0.8;
            }
        }

        if (($weights['color'] ?? 0) > 0 && isset($preferences['color']) && is_array($preferences['color'])) {
            $wineColor = $wine->color instanceof WineColor ? $wine->color->value : (string) $wine->color;
            if (in_array($wineColor, $preferences['color'], true)) {
                $score += $weights['color'];
            }
        }

        if (($weights['grape'] ?? 0) > 0 && ! empty($preferences['grape']) && $preferences['grape'] !== 'any') {
            if ($wine->grape && strcasecmp($wine->grape, (string) $preferences['grape']) === 0) {
                $score += $weights['grape'];
            }
        }

        if (($weights['region'] ?? 0) > 0 && ! empty($preferences['region']) && $preferences['region'] !== 'any') {
            if ($wine->region && strcasecmp($wine->region, (string) $preferences['region']) === 0) {
                $score += $weights['region'];
            }
        }

        if (($weights['max_price'] ?? 0) > 0 && isset($preferences['max_price']) && $wine->price !== null) {
            $maxPrice = (float) $preferences['max_price'];
            $winePrice = $wine->price / 100;

            if ($winePrice <= $maxPrice) {
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
    public function getExplanation(Wine $wine, array $preferences): string
    {
        $reasons = [];
        $activeQuestions = $preferences['_active_questions'] ?? RecommendationQuestions::DEFAULT_WINE;

        if (in_array('aromas', $activeQuestions, true) && isset($preferences['aromas']) && is_array($preferences['aromas'])) {
            $wineTagSlugs = $wine->tags->pluck('slug')->toArray();
            $matchingTags = array_intersect($preferences['aromas'], $wineTagSlugs);

            if (! empty($matchingTags)) {
                $tagNames = $wine->tags->whereIn('slug', $matchingTags)->pluck('name')->join(', ');
                $reasons[] = "Correspond à vos arômes préférés : {$tagNames}";
            }
        }

        if (in_array('food', $activeQuestions, true) && isset($preferences['food_pairings']) && is_array($preferences['food_pairings'])) {
            $winePairings = is_array($wine->food_pairings) ? $wine->food_pairings : [];
            $matching = array_intersect($preferences['food_pairings'], $winePairings);

            if (! empty($matching)) {
                $reasons[] = 'Accord recommandé avec votre plat';
            }
        }

        if (in_array('max_abv', $activeQuestions, true) && isset($preferences['max_abv'])) {
            $maxAbv = (float) $preferences['max_abv'];
            if ($wine->abv <= $maxAbv) {
                $reasons[] = "Taux d'alcool adapté ({$wine->abv}%)";
            } elseif ($this->isNearMiss($wine->abv, $maxAbv, self::ABV_TOLERANCE_ABS)) {
                $reasons[] = "Taux d'alcool légèrement au-dessus de votre seuil ({$wine->abv}%)";
            }
        }

        if (in_array('color', $activeQuestions, true) && isset($preferences['color']) && is_array($preferences['color'])) {
            $colorValue = $wine->color instanceof WineColor ? $wine->color->value : (string) $wine->color;
            if (in_array($colorValue, $preferences['color'], true)) {
                $colorLabel = $wine->color instanceof WineColor
                    ? $wine->color->label()
                    : (WineColor::tryFrom($colorValue)?->label() ?? ucfirst($colorValue));
                $reasons[] = "Couleur correspondant à vos préférences ({$colorLabel})";
            }
        }

        if (in_array('grape', $activeQuestions, true) && ! empty($preferences['grape']) && $preferences['grape'] !== 'any') {
            if ($wine->grape && strcasecmp($wine->grape, (string) $preferences['grape']) === 0) {
                $reasons[] = "Cépage correspondant à vos préférences ({$wine->grape})";
            }
        }

        if (in_array('region', $activeQuestions, true) && ! empty($preferences['region']) && $preferences['region'] !== 'any') {
            if ($wine->region && strcasecmp($wine->region, (string) $preferences['region']) === 0) {
                $reasons[] = "Région correspondant à vos préférences ({$wine->region})";
            }
        }

        if (in_array('max_price', $activeQuestions, true) && isset($preferences['max_price']) && $wine->price !== null) {
            $maxPrice = (float) $preferences['max_price'];
            $winePrice = $wine->price / 100;
            if ($winePrice <= $maxPrice) {
                $reasons[] = "Prix adapté à votre budget (≤ {$maxPrice}€)";
            } elseif ($this->isNearMiss($winePrice, $maxPrice, self::PRICE_TOLERANCE_ABS)) {
                $reasons[] = "Prix légèrement au-dessus de votre budget ({$winePrice}€)";
            }
        }

        if (empty($reasons)) {
            return 'Sélection basée sur le profil du vin';
        }

        return implode('. ', $reasons).'.';
    }

    /**
     * Check if wine respects hard limits (with tolerance).
     */
    protected function passesHardLimits(Wine $wine, array $preferences): bool
    {
        if (isset($preferences['max_abv'])) {
            $maxAbv = (float) $preferences['max_abv'];
            $maxAllowedAbv = $this->maxAllowed($maxAbv, self::ABV_TOLERANCE_ABS);
            if ($maxAbv > 0 && $wine->abv > $maxAllowedAbv) {
                return false;
            }
        }

        if (isset($preferences['max_price'])) {
            $maxPrice = (float) $preferences['max_price'];
            if ($maxPrice > 0) {
                if ($wine->price === null) {
                    return false;
                }
                $winePrice = $wine->price / 100;
                $maxAllowedPrice = $this->maxAllowed($maxPrice, self::PRICE_TOLERANCE_ABS);
                if ($winePrice > $maxAllowedPrice) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function isNearMiss(float $value, float $max, float $absoluteTolerance = self::ABV_TOLERANCE_ABS): bool
    {
        if ($max <= 0) {
            return false;
        }

        return $value > $max && $value <= $this->maxAllowed($max, $absoluteTolerance);
    }

    protected function maxAllowed(float $max, float $absoluteTolerance): float
    {
        return $max + max($max * self::TOLERANCE_RATIO, $absoluteTolerance);
    }

    /**
     * Ensure the recommendation is close enough overall.
     */
    protected function passesOverallThreshold(Wine $wine, array $preferences): bool
    {
        $quality = $this->evaluateMatchQuality($wine, $preferences);
        if ($quality['total'] === 0.0) {
            return true;
        }

        if ($quality['strict'] <= 0 && $quality['near'] > 0) {
            return false;
        }

        $score = ($quality['strict'] + ($quality['near'] * self::NEAR_WEIGHT)) / $quality['total'];

        return $score >= self::QUALITY_THRESHOLD;
    }

    /**
     * @return array{strict:float, near:float, total:float}
     */
    protected function evaluateMatchQuality(Wine $wine, array $preferences): array
    {
        $strict = 0.0;
        $near = 0.0;
        $total = 0.0;
        $activeQuestions = $preferences['_active_questions'] ?? RecommendationQuestions::DEFAULT_WINE;
        $weights = $this->normalizeWeights($activeQuestions);

        if (in_array('aromas', $activeQuestions, true) && isset($preferences['aromas']) && is_array($preferences['aromas'])) {
            $weight = $weights['aromas'] ?? 0.0;
            $total += $weight;
            $wineTagSlugs = $wine->tags->pluck('slug')->toArray();
            if (count(array_intersect($preferences['aromas'], $wineTagSlugs)) > 0) {
                $strict += $weight;
            }
        }

        if (in_array('food', $activeQuestions, true) && isset($preferences['food_pairings']) && is_array($preferences['food_pairings'])) {
            $weight = $weights['food'] ?? 0.0;
            $total += $weight;
            $winePairings = is_array($wine->food_pairings) ? $wine->food_pairings : [];
            if (count(array_intersect($preferences['food_pairings'], $winePairings)) > 0) {
                $strict += $weight;
            }
        }

        if (in_array('max_abv', $activeQuestions, true) && isset($preferences['max_abv'])) {
            $weight = $weights['max_abv'] ?? 0.0;
            $total += $weight;
            $maxAbv = (float) $preferences['max_abv'];
            if ($wine->abv <= $maxAbv) {
                $strict += $weight;
            } elseif ($this->isNearMiss($wine->abv, $maxAbv, self::ABV_TOLERANCE_ABS)) {
                $near += $weight;
            }
        }

        if (in_array('color', $activeQuestions, true) && isset($preferences['color']) && is_array($preferences['color'])) {
            $weight = $weights['color'] ?? 0.0;
            $total += $weight;
            $wineColor = $wine->color instanceof WineColor ? $wine->color->value : (string) $wine->color;
            if (in_array($wineColor, $preferences['color'], true)) {
                $strict += $weight;
            }
        }

        if (in_array('grape', $activeQuestions, true) && ! empty($preferences['grape']) && $preferences['grape'] !== 'any') {
            $weight = $weights['grape'] ?? 0.0;
            $total += $weight;
            if ($wine->grape && strcasecmp($wine->grape, (string) $preferences['grape']) === 0) {
                $strict += $weight;
            }
        }

        if (in_array('region', $activeQuestions, true) && ! empty($preferences['region']) && $preferences['region'] !== 'any') {
            $weight = $weights['region'] ?? 0.0;
            $total += $weight;
            if ($wine->region && strcasecmp($wine->region, (string) $preferences['region']) === 0) {
                $strict += $weight;
            }
        }

        if (in_array('max_price', $activeQuestions, true) && isset($preferences['max_price'])) {
            $weight = $weights['max_price'] ?? 0.0;
            $total += $weight;
            $maxPrice = (float) $preferences['max_price'];
            if ($wine->price !== null) {
                $winePrice = $wine->price / 100;
                if ($winePrice <= $maxPrice) {
                    $strict += $weight;
                } elseif ($this->isNearMiss($winePrice, $maxPrice, self::PRICE_TOLERANCE_ABS)) {
                    $near += $weight;
                }
            }
        }

        return [
            'strict' => $strict,
            'near' => $near,
            'total' => $total,
        ];
    }

    /**
     * @param  array<int, string>  $activeQuestions
     * @return array<string, float>
     */
    protected function normalizeWeights(array $activeQuestions): array
    {
        $baseWeights = RecommendationQuestions::weights('wine');
        $activeWeights = array_intersect_key($baseWeights, array_flip($activeQuestions));

        $total = array_sum($activeWeights);
        if ($total <= 0) {
            return [];
        }

        return array_map(fn (float $weight) => ($weight / $total) * 100, $activeWeights);
    }
}

