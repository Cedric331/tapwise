<?php

namespace App\Services;

use App\Enums\BeerColor;
use App\Models\Bar;
use App\Models\Beer;
use App\Support\RecommendationQuestions;
use Illuminate\Support\Collection;

class BeerRecommendationService
{
    private const TOLERANCE_RATIO = 0.10;

    private const ABV_TOLERANCE_ABS = 0.5;

    private const PRICE_TOLERANCE_ABS = 0.5;

    private const NEAR_WEIGHT = 0.5;

    private const QUALITY_THRESHOLD = 0.9;

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

        $beers = $beers->filter(fn (Beer $beer) => $this->passesHardLimits($beer, $preferences));

        if ($beers->isEmpty()) {
            return collect();
        }

        $beers = $beers->filter(fn (Beer $beer) => $this->passesOverallThreshold($beer, $preferences));

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
        $activeQuestions = $preferences['_active_questions'] ?? RecommendationQuestions::DEFAULT_BEER;
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
            } elseif ($format === 'bouteille' && ! $beer->is_on_tap) {
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
        $activeQuestions = $preferences['_active_questions'] ?? RecommendationQuestions::DEFAULT_BEER;

        // Check tag matches
        if (in_array('aromas', $activeQuestions, true) && isset($preferences['aromas']) && is_array($preferences['aromas'])) {
            $beerTagSlugs = $beer->tags->pluck('slug')->toArray();
            $matchingTags = array_intersect($preferences['aromas'], $beerTagSlugs);

            if (! empty($matchingTags)) {
                $tagNames = $beer->tags->whereIn('slug', $matchingTags)->pluck('name')->join(', ');
                $reasons[] = "Correspond à vos arômes préférés : {$tagNames}";
            }
        }

        // Check ABV
        if (in_array('max_abv', $activeQuestions, true) && isset($preferences['max_abv'])) {
            $maxAbv = (float) $preferences['max_abv'];
            if ($beer->abv <= $maxAbv) {
                $reasons[] = "Taux d'alcool adapté ({$beer->abv}%)";
            } elseif ($this->isNearMiss($beer->abv, $maxAbv)) {
                $reasons[] = "Taux d'alcool légèrement au-dessus de votre seuil ({$beer->abv}%)";
            }
        }

        // Check bitterness
        if (in_array('bitterness', $activeQuestions, true) && isset($preferences['bitterness']) && $beer->ibu !== null) {
            $bitterness = $preferences['bitterness'];
            $ibu = $beer->ibu;

            if (($bitterness === 'faible' && $ibu <= 25) ||
                ($bitterness === 'moyenne' && $ibu > 25 && $ibu <= 50) ||
                ($bitterness === 'forte' && $ibu > 50)) {
                $reasons[] = 'Amertume correspondant à vos préférences';
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
                $reasons[] = 'Disponible à la pression';
            } elseif ($preferences['format'] === 'bouteille' && ! $beer->is_on_tap) {
                $reasons[] = 'Disponible en bouteille';
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
            } elseif ($this->isNearMiss($beerPrice, $maxPrice, self::PRICE_TOLERANCE_ABS)) {
                $reasons[] = "Prix légèrement au-dessus de votre budget ({$beerPrice}€)";
            }
        }

        if (empty($reasons)) {
            return 'Sélection basée sur le profil de la bière';
        }

        return implode('. ', $reasons).'.';
    }

    /**
     * Check if beer respects hard limits (with tolerance).
     */
    protected function passesHardLimits(Beer $beer, array $preferences): bool
    {
        if (isset($preferences['max_abv'])) {
            $maxAbv = (float) $preferences['max_abv'];
            $maxAllowedAbv = $this->maxAllowed($maxAbv, self::ABV_TOLERANCE_ABS);
            if ($maxAbv > 0 && $beer->abv > $maxAllowedAbv) {
                return false;
            }
        }

        if (isset($preferences['max_price'])) {
            $maxPrice = (float) $preferences['max_price'];
            if ($maxPrice > 0) {
                if ($beer->price === null) {
                    return false;
                }
                $beerPrice = $beer->price / 100;
                $maxAllowedPrice = $this->maxAllowed($maxPrice, self::PRICE_TOLERANCE_ABS);
                if ($beerPrice > $maxAllowedPrice) {
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
    protected function passesOverallThreshold(Beer $beer, array $preferences): bool
    {
        $quality = $this->evaluateMatchQuality($beer, $preferences);
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
    protected function evaluateMatchQuality(Beer $beer, array $preferences): array
    {
        $strict = 0.0;
        $near = 0.0;
        $total = 0.0;
        $activeQuestions = $preferences['_active_questions'] ?? RecommendationQuestions::DEFAULT_BEER;
        $weights = $this->normalizeWeights($activeQuestions);

        if (in_array('aromas', $activeQuestions, true) && isset($preferences['aromas']) && is_array($preferences['aromas'])) {
            $weight = $weights['aromas'] ?? 0.0;
            $total += $weight;
            $beerTagSlugs = $beer->tags->pluck('slug')->toArray();
            if (count(array_intersect($preferences['aromas'], $beerTagSlugs)) > 0) {
                $strict += $weight;
            }
        }

        if (in_array('max_abv', $activeQuestions, true) && isset($preferences['max_abv'])) {
            $weight = $weights['max_abv'] ?? 0.0;
            $total += $weight;
            $maxAbv = (float) $preferences['max_abv'];
            if ($beer->abv <= $maxAbv) {
                $strict += $weight;
            } elseif ($this->isNearMiss($beer->abv, $maxAbv, self::ABV_TOLERANCE_ABS)) {
                $near += $weight;
            }
        }

        if (in_array('bitterness', $activeQuestions, true) && isset($preferences['bitterness']) && $beer->ibu !== null) {
            $weight = $weights['bitterness'] ?? 0.0;
            $total += $weight;
            $bitterness = $preferences['bitterness'];
            $ibu = $beer->ibu;
            if (($bitterness === 'faible' && $ibu <= 25) ||
                ($bitterness === 'moyenne' && $ibu > 25 && $ibu <= 50) ||
                ($bitterness === 'forte' && $ibu > 50)) {
                $strict += $weight;
            }
        }

        if (in_array('format', $activeQuestions, true) && isset($preferences['format'])) {
            $weight = $weights['format'] ?? 0.0;
            $total += $weight;
            $format = $preferences['format'];
            if (($format === 'pression' && $beer->is_on_tap) || ($format === 'bouteille' && ! $beer->is_on_tap)) {
                $strict += $weight;
            }
        }

        if (in_array('color', $activeQuestions, true) && isset($preferences['color']) && is_array($preferences['color'])) {
            $weight = $weights['color'] ?? 0.0;
            $total += $weight;
            $beerColor = $beer->color instanceof BeerColor ? $beer->color->value : (string) $beer->color;
            if (in_array($beerColor, $preferences['color'], true)) {
                $strict += $weight;
            }
        }

        if (in_array('style', $activeQuestions, true) && ! empty($preferences['style']) && $preferences['style'] !== 'any') {
            $weight = $weights['style'] ?? 0.0;
            $total += $weight;
            if ($beer->style && strcasecmp($beer->style, (string) $preferences['style']) === 0) {
                $strict += $weight;
            }
        }

        if (in_array('brewery', $activeQuestions, true) && ! empty($preferences['brewery']) && $preferences['brewery'] !== 'any') {
            $weight = $weights['brewery'] ?? 0.0;
            $total += $weight;
            if ($beer->brewery && strcasecmp($beer->brewery, (string) $preferences['brewery']) === 0) {
                $strict += $weight;
            }
        }

        if (in_array('max_price', $activeQuestions, true) && isset($preferences['max_price'])) {
            $weight = $weights['max_price'] ?? 0.0;
            $total += $weight;
            $maxPrice = (float) $preferences['max_price'];
            if ($beer->price !== null) {
                $beerPrice = $beer->price / 100;
                if ($beerPrice <= $maxPrice) {
                    $strict += $weight;
                } elseif ($this->isNearMiss($beerPrice, $maxPrice, self::PRICE_TOLERANCE_ABS)) {
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
        $baseWeights = RecommendationQuestions::weights('beer');
        $activeWeights = array_intersect_key($baseWeights, array_flip($activeQuestions));

        $total = array_sum($activeWeights);
        if ($total <= 0) {
            return [];
        }

        return array_map(fn (float $weight) => ($weight / $total) * 100, $activeWeights);
    }
}
