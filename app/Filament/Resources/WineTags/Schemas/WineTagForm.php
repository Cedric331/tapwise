<?php

namespace App\Filament\Resources\WineTags\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WineTagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->maxLength(255),
            ]);
    }
}
