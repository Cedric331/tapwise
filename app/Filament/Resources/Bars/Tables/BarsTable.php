<?php

namespace App\Filament\Resources\Bars\Tables;

use App\Models\Bar;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('subscription_status')
                    ->label('Abonnement')
                    ->getStateUsing(fn (Bar $record) => $record->subscriptionStatus()),
                TextColumn::make('users_count')
                    ->label('Utilisateurs')
                    ->counts('users'),
                TextColumn::make('count_scans')
                    ->label('Scans')
                    ->sortable(),
                IconColumn::make('qr_enabled')
                    ->label('QR')
                    ->boolean(),
                IconColumn::make('is_demo')
                    ->label('Démo')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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

