<?php

namespace App\Support;

use Illuminate\Support\Str;

class WineFoodPairings
{
    /**
     * @return array<int, array{id: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['id' => 'viande_rouge', 'label' => 'Viande rouge'],
            ['id' => 'viande_blanche', 'label' => 'Viande blanche'],
            ['id' => 'poisson', 'label' => 'Poisson'],
            ['id' => 'fruits_de_mer', 'label' => 'Fruits de mer'],
            ['id' => 'charcuterie', 'label' => 'Charcuterie'],
            ['id' => 'fromage', 'label' => 'Fromage'],
            ['id' => 'vegetarien', 'label' => 'Végétarien'],
            ['id' => 'dessert', 'label' => 'Dessert'],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function ids(): array
    {
        return array_map(fn (array $option) => $option['id'], self::options());
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        $labels = [];

        foreach (self::options() as $option) {
            $labels[$option['id']] = $option['label'];
        }

        return $labels;
    }

    /**
     * @return array<int, string>
     */
    public static function normalizeInput($value): array
    {
        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        $labels = self::labels();
        $optionsBySlug = [];
        foreach ($labels as $id => $label) {
            $optionsBySlug[Str::slug($label, '_')] = $id;
            $optionsBySlug[$id] = $id;
        }

        $rawParts = preg_split('/[;,|]/', $value) ?: [];
        $normalized = [];

        foreach ($rawParts as $part) {
            $slug = Str::slug($part, '_');
            if ($slug === '') {
                continue;
            }

            if (isset($optionsBySlug[$slug])) {
                $normalized[] = $optionsBySlug[$slug];
            }
        }

        return array_values(array_unique($normalized));
    }
}
