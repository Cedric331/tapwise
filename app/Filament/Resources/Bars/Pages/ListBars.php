<?php

namespace App\Filament\Resources\Bars\Pages;

use App\Filament\Resources\Bars\BarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBars extends ListRecords
{
    protected static string $resource = BarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
