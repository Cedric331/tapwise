<?php

namespace App\Enums;

enum BeerColor: string
{
    case WHITE  = 'white';
    case BLONDE = 'blonde';
    case GOLDEN = 'golden';
    case AMBER  = 'amber';
    case RED    = 'red';
    case BROWN  = 'brown';
    case BLACK  = 'black';

    /**
     * Human-readable label (FR)
     */
    public function label(): string
    {
        return match ($this) {
            self::WHITE  => 'Blanche',
            self::BLONDE => 'Blonde',
            self::GOLDEN => 'Dorée',
            self::AMBER  => 'Ambrée',
            self::RED    => 'Rousse',
            self::BROWN  => 'Brune',
            self::BLACK  => 'Noire',
        };
    }

    /**
     * Select options (value => label)
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->toArray();
    }
}
