<?php

namespace App\Filament\Resources\Bars;

use App\Filament\Resources\Bars\Pages\CreateBar;
use App\Filament\Resources\Bars\Pages\EditBar;
use App\Filament\Resources\Bars\Pages\ListBars;
use App\Filament\Resources\Bars\Schemas\BarForm;
use App\Filament\Resources\Bars\Tables\BarsTable;
use App\Models\Bar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BarResource extends Resource
{
    protected static ?string $model = Bar::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Bars';

    public static function form(Schema $schema): Schema
    {
        return BarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BarsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBars::route('/'),
            'create' => CreateBar::route('/create'),
            'edit' => EditBar::route('/{record}/edit'),
        ];
    }
}
