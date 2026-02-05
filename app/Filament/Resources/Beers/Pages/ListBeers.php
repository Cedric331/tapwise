<?php

namespace App\Filament\Resources\Beers\Pages;

use App\Filament\Imports\BeerImporter;
use App\Filament\Resources\Beers\BeerResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListBeers extends ListRecords
{
    protected static string $resource = BeerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->label('Importer (CSV)')
                ->modalDescription('Exportez votre fichier Excel en CSV avant import.')
                ->importer(BeerImporter::class),
        ];
    }
}

