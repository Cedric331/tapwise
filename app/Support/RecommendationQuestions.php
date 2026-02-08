<?php

namespace App\Support;

class RecommendationQuestions
{
    public const DEFAULT_BEER = [
        'bitterness',
        'color',
        'aromas',
        'max_abv',
        'format',
    ];

    public const DEFAULT_WINE = [
        'color',
        'food',
        'grape',
        'region',
        'max_abv',
        'max_price',
    ];

    /**
     * @return array<int, array{id: string, label: string, description: string}>
     */
    public static function all(string $type = 'beer'): array
    {
        if ($type === 'wine') {
            return [
                [
                    'id' => 'color',
                    'label' => 'Couleur',
                    'description' => 'Couleurs de vin appréciées',
                ],
                [
                    'id' => 'food',
                    'label' => 'Accords mets',
                    'description' => 'Quel plat accompagne votre vin',
                ],
                [
                    'id' => 'grape',
                    'label' => 'Cépage',
                    'description' => 'Cépages préférés',
                ],
                [
                    'id' => 'region',
                    'label' => 'Région',
                    'description' => 'Régions ou appellations favorites',
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
                    'id' => 'max_price',
                    'label' => 'Budget maximum',
                    'description' => 'Prix maximal par verre/bouteille',
                ],
            ];
        }

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
    public static function ids(string $type = 'beer'): array
    {
        return array_map(fn (array $question) => $question['id'], self::all($type));
    }

    /**
     * @param  array<int, string>|null  $selected
     * @return array<int, string>
     */
    public static function normalizeSelected(?array $selected, string $type = 'beer'): array
    {
        if (! is_array($selected) || $selected === []) {
            return $type === 'wine' ? self::DEFAULT_WINE : self::DEFAULT_BEER;
        }

        $ids = self::ids($type);
        $filtered = array_values(array_unique(array_filter(
            $selected,
            fn ($id) => is_string($id) && in_array($id, $ids, true)
        )));

        if (count($filtered) < 3) {
            return $type === 'wine' ? self::DEFAULT_WINE : self::DEFAULT_BEER;
        }

        return array_slice($filtered, 0, 10);
    }

    /**
     * @return array<string, float>
     */
    public static function weights(string $type = 'beer'): array
    {
        if ($type === 'wine') {
            return [
                'aromas' => 25,
                'max_abv' => 20,
                'color' => 15,
                'food' => 20,
                'grape' => 15,
                'region' => 10,
                'max_price' => 10,
            ];
        }

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
                case 'grape':
                case 'region':
                    if (! empty($preferences[$id]) && $preferences[$id] !== 'any') {
                        $active[] = $id;
                    }
                    break;
                case 'food':
                    if (isset($preferences['food_pairings']) && is_array($preferences['food_pairings']) && count($preferences['food_pairings']) > 0) {
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
