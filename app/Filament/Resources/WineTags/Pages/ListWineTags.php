<?php

namespace App\Filament\Resources\WineTags\Pages;

use App\Filament\Resources\WineTags\WineTagResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWineTags extends ListRecords
{
    protected static string $resource = WineTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

