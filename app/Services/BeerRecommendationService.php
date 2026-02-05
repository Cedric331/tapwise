<?php

namespace App\Services;

use App\Enums\BeerColor;
use App\Models\Bar;
use App\Models\Beer;
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

        // Tag matching (30% weight)
        if (isset($preferences['aromas']) && is_array($preferences['aromas'])) {
            $beerTagSlugs = $beer->tags->pluck('slug')->toArray();
            $matchingTags = count(array_intersect($preferences['aromas'], $beerTagSlugs));
            $totalTags = count($preferences['aromas']);
            
            if ($totalTags > 0) {
                $score += ($matchingTags / $totalTags) * 30;
            }
        }

        // ABV matching (25% weight)
        if (isset($preferences['max_abv'])) {
            $maxAbv = (float) $preferences['max_abv'];
            $beerAbv = $beer->abv;
            
            if ($beerAbv <= $maxAbv) {
                // Prefer beers closer to max ABV
                $score += (($beerAbv / max($maxAbv, 1)) * 25);
            } else {
                // Penalize beers over max ABV
                $score -= 20;
            }
        }

        // Bitterness matching (20% weight)
        if (isset($preferences['bitterness']) && $beer->ibu !== null) {
            $bitterness = $preferences['bitterness'];
            $ibu = $beer->ibu;
            
            if ($bitterness === 'faible' && $ibu <= 25) {
                $score += 20;
            } elseif ($bitterness === 'moyenne' && $ibu > 25 && $ibu <= 50) {
                $score += 20;
            } elseif ($bitterness === 'forte' && $ibu > 50) {
                $score += 20;
            }
        }

        // Format preference (10% weight)
        if (isset($preferences['format'])) {
            $format = $preferences['format'];
            
            if ($format === 'pression' && $beer->is_on_tap) {
                $score += 10;
            } elseif ($format === 'bouteille' && !$beer->is_on_tap) {
                $score += 10;
            }
        }

        // Color preference (15% weight)
        if (isset($preferences['color']) && is_array($preferences['color'])) {
            $beerColor = $beer->color instanceof BeerColor ? $beer->color->value : (string) $beer->color;
            if (in_array($beerColor, $preferences['color'], true)) {
                $score += 15;
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

        // Check tag matches
        if (isset($preferences['aromas']) && is_array($preferences['aromas'])) {
            $beerTagSlugs = $beer->tags->pluck('slug')->toArray();
            $matchingTags = array_intersect($preferences['aromas'], $beerTagSlugs);
            
            if (!empty($matchingTags)) {
                $tagNames = $beer->tags->whereIn('slug', $matchingTags)->pluck('name')->join(', ');
                $reasons[] = "Correspond à vos arômes préférés : {$tagNames}";
            }
        }

        // Check ABV
        if (isset($preferences['max_abv'])) {
            $maxAbv = (float) $preferences['max_abv'];
            if ($beer->abv <= $maxAbv) {
                $reasons[] = "Taux d'alcool adapté ({$beer->abv}%)";
            }
        }

        // Check bitterness
        if (isset($preferences['bitterness']) && $beer->ibu !== null) {
            $bitterness = $preferences['bitterness'];
            $ibu = $beer->ibu;
            
            if (($bitterness === 'faible' && $ibu <= 25) ||
                ($bitterness === 'moyenne' && $ibu > 25 && $ibu <= 50) ||
                ($bitterness === 'forte' && $ibu > 50)) {
                $reasons[] = "Amertume correspondant à vos préférences";
            }
        }

        if (isset($preferences['color']) && is_array($preferences['color'])) {
            $colorValue = $beer->color instanceof BeerColor ? $beer->color->value : (string) $beer->color;
            if (in_array($colorValue, $preferences['color'], true)) {
                $colorLabel = $beer->color instanceof BeerColor
                    ? $beer->color->label()
                    : (BeerColor::tryFrom($colorValue)?->label() ?? ucfirst($colorValue));
                $reasons[] = "Couleur correspondant à vos préférences ({$colorLabel})";
            }
        }

        if (empty($reasons)) {
            return "Une bière de qualité qui pourrait vous plaire.";
        }

        return implode('. ', $reasons) . '.';
    }
}

