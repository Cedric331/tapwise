<?php

namespace App\Filament\Resources\Bars\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('logo_path')
                    ->label('Logo')
                    ->disk('public')
                    ->directory('bars/logos')
                    ->image()
                    ->maxSize(2048),
                TextInput::make('brand_background_color')
                    ->label('Couleur de fond (hex)')
                    ->maxLength(7),
                TextInput::make('brand_primary_color')
                    ->label('Couleur primaire (hex)')
                    ->maxLength(7),
                Textarea::make('welcome_message')
                    ->label('Message de bienvenue')
                    ->columnSpanFull(),
                Toggle::make('qr_enabled')
                    ->label('QR activé')
                    ->required(),
                TextInput::make('count_scans')
                    ->label('Nombre de scans')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),
                Toggle::make('is_demo')
                    ->label('Démo')
                    ->required(),
            ]);
    }
}
