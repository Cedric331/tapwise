<?php

namespace App\Enums;

enum WineColor: string
{
    case RED = 'red';
    case WHITE = 'white';
    case ROSE = 'rose';

    /**
     * Human-readable label (FR)
     */
    public function label(): string
    {
        return match ($this) {
            self::RED => 'Rouge',
            self::WHITE => 'Blanc',
            self::ROSE => 'Rosé',
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

