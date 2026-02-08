<?php

namespace App\Filament\Resources\WineTags;

use App\Filament\Resources\WineTags\Pages\CreateWineTag;
use App\Filament\Resources\WineTags\Pages\EditWineTag;
use App\Filament\Resources\WineTags\Pages\ListWineTags;
use App\Filament\Resources\WineTags\Schemas\WineTagForm;
use App\Filament\Resources\WineTags\Tables\WineTagsTable;
use App\Models\WineTag;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WineTagResource extends Resource
{
    protected static ?string $model = WineTag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Tags vins';

    public static function form(Schema $schema): Schema
    {
        return WineTagForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WineTagsTable::configure($table);
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
            'index' => ListWineTags::route('/'),
            'create' => CreateWineTag::route('/create'),
            'edit' => EditWineTag::route('/{record}/edit'),
        ];
    }
}
