<?php

namespace App\Filament\Resources\Beers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BeersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bar.name')
                    ->label('Bar')
                    ->sortable(),
                TextColumn::make('style')
                    ->searchable(),
                TextColumn::make('color')
                    ->label('Couleur'),
                TextColumn::make('abv_x10')
                    ->label('ABV')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state / 10, 1, ',', ' ') . ' %' : '-'),
                TextColumn::make('price')
                    ->label('Prix')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state / 100, 2, ',', ' ') . ' €' : '-'),
                IconColumn::make('is_on_tap')
                    ->label('Pression')
                    ->boolean(),
                IconColumn::make('is_available')
                    ->label('Disponible')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

