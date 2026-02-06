<?php

namespace App\Support;

class RecommendationQuestions
{
    public const DEFAULT = [
        'bitterness',
        'color',
        'aromas',
        'max_abv',
        'format',
    ];

    /**
     * @return array<int, array{id: string, label: string, description: string}>
     */
    public static function all(): array
    {
        return [
            [
                'id' => 'bitterness',
                'label' => 'Amertume',
                'description' => "Niveau d'amertume préféré",
            ],
            [
                'id' => 'color',
                'label' => 'Couleur',
                'description' => 'Couleurs de bière appréciées',
            ],
            [
                'id' => 'aromas',
                'label' => 'Arômes',
                'description' => 'Arômes qui vous plaisent',
            ],
            [
                'id' => 'max_abv',
                'label' => "Degré d'alcool maximum",
                'description' => "Taux d'alcool souhaité",
            ],
            [
                'id' => 'format',
                'label' => 'Format',
                'description' => 'Pression ou bouteille',
            ],
            [
                'id' => 'style',
                'label' => 'Style de bière',
                'description' => 'Styles favoris',
            ],
            [
                'id' => 'brewery',
                'label' => 'Brasserie',
                'description' => 'Brasseries préférées',
            ],
            [
                'id' => 'max_price',
                'label' => 'Budget maximum',
                'description' => 'Prix maximal par bière',
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function ids(): array
    {
        return array_map(fn (array $question) => $question['id'], self::all());
    }

    /**
     * @param  array<int, string>|null  $selected
     * @return array<int, string>
     */
    public static function normalizeSelected(?array $selected): array
    {
        if (! is_array($selected) || $selected === []) {
            return self::DEFAULT;
        }

        $ids = self::ids();
        $filtered = array_values(array_unique(array_filter(
            $selected,
            fn ($id) => is_string($id) && in_array($id, $ids, true)
        )));

        if (count($filtered) < 3) {
            return self::DEFAULT;
        }

        return array_slice($filtered, 0, 10);
    }

    /**
     * @return array<string, float>
     */
    public static function weights(): array
    {
        return [
            'aromas' => 30,
            'max_abv' => 25,
            'bitterness' => 20,
            'color' => 15,
            'format' => 10,
            'style' => 15,
            'brewery' => 10,
            'max_price' => 15,
        ];
    }

    /**
     * @param  array<int, string>  $selected
     * @param  array<string, mixed>  $preferences
     * @return array<int, string>
     */
    public static function resolveActiveQuestions(array $selected, array $preferences): array
    {
        $active = [];

        foreach ($selected as $id) {
            switch ($id) {
                case 'aromas':
                case 'color':
                    if (isset($preferences[$id]) && is_array($preferences[$id]) && count($preferences[$id]) > 0) {
                        $active[] = $id;
                    }
                    break;
                case 'style':
                case 'brewery':
                    if (! empty($preferences[$id]) && $preferences[$id] !== 'any') {
                        $active[] = $id;
                    }
                    break;
                case 'max_price':
                case 'max_abv':
                    if (isset($preferences[$id])) {
                        $active[] = $id;
                    }
                    break;
                default:
                    if (! empty($preferences[$id])) {
                        $active[] = $id;
                    }
                    break;
            }
        }

        return $active;
    }
}
