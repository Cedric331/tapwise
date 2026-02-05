<?php

namespace App\Filament\Resources\Beers\Schemas;

use App\Enums\BeerColor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BeerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('bar_id')
                    ->relationship('bar', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('brewery')
                    ->maxLength(255),
                TextInput::make('style')
                    ->required()
                    ->maxLength(255),
                Select::make('color')
                    ->label('Couleur')
                    ->options(BeerColor::options())
                    ->required(),
                TextInput::make('abv_x10')
                    ->label('ABV (x10)')
                    ->numeric()
                    ->required(),
                TextInput::make('ibu')
                    ->numeric(),
                TextInput::make('price')
                    ->label('Prix (centimes)')
                    ->numeric()
                    ->helperText('Ex: 650 pour 6,50 €'),
                Toggle::make('is_on_tap')
                    ->label('Pression'),
                Toggle::make('is_available')
                    ->label('Disponible'),
                Select::make('tags')
                    ->label('Tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload(),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }
}

