<?php

namespace App\Filament\Imports;

use App\Enums\BeerColor;
use App\Models\Bar;
use App\Models\Beer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BeerImporter extends Importer
{
    protected static ?string $model = Beer::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nom')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->guess(['nom', 'biere', 'bière', 'nom biere', 'nom bière']),
            ImportColumn::make('brewery')
                ->label('Brasserie')
                ->rules(['nullable', 'max:255'])
                ->guess(['brewery', 'brasserie', 'producteur', 'brasseur']),
            ImportColumn::make('style')
                ->label('Style')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->guess(['style', 'type']),
            ImportColumn::make('color')
                ->label('Couleur')
                ->requiredMapping()
                ->rules(['required', Rule::in(array_map(fn (BeerColor $color) => $color->value, BeerColor::cases()))])
                ->guess(['color', 'couleur', 'robe'])
                ->castStateUsing(fn ($state) => self::normalizeColor($state)),
            ImportColumn::make('abv_x10')
                ->label('ABV')
                ->requiredMapping()
                ->rules(['required', 'integer', 'min:0', 'max:200'])
                ->guess(['abv', 'alcool', 'degre', 'degré', 'taux', 'vol', 'abv_x10', 'abv x10', 'abv10', 'degre x10', 'degré x10'])
                ->castStateUsing(fn ($state) => self::normalizeAbvX10($state)),
            ImportColumn::make('ibu')
                ->label('IBU')
                ->rules(['nullable', 'integer', 'min:0', 'max:120'])
                ->guess(['ibu', 'amertume'])
                ->castStateUsing(fn ($state) => self::normalizeInt($state)),
            ImportColumn::make('description')
                ->label('Description')
                ->rules(['nullable', 'string'])
                ->guess(['description', 'notes']),
            ImportColumn::make('is_on_tap')
                ->label('Pression')
                ->rules(['nullable', 'boolean'])
                ->guess(['is_on_tap', 'pression', 'draft', 'on tap', 'tirage'])
                ->castStateUsing(fn ($state) => self::normalizeBoolean($state))
                ->ignoreBlankState(),
            ImportColumn::make('is_available')
                ->label('Disponible')
                ->rules(['nullable', 'boolean'])
                ->guess(['is_available', 'disponible', 'dispo', 'available', 'actif'])
                ->castStateUsing(fn ($state) => self::normalizeBoolean($state))
                ->ignoreBlankState(),
            ImportColumn::make('price')
                ->label('Prix')
                ->rules(['nullable', 'integer', 'min:0'])
                ->guess(['price', 'prix', 'tarif', 'prix ttc'])
                ->castStateUsing(fn ($state) => self::normalizePrice($state)),
        ];
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            Select::make('bar_id')
                ->label('Bar')
                ->options(Bar::query()->orderBy('name')->pluck('name', 'id')->all())
                ->searchable()
                ->required(),
        ];
    }

    public function resolveRecord(): ?Model
    {
        return new Beer;
    }

    protected function beforeFill(): void
    {
        $barId = $this->getOptions()['bar_id'] ?? null;

        if ($this->record && $barId) {
            $this->record->bar_id = $barId;
        }
    }

    protected function beforeSave(): void
    {
        if (! $this->record) {
            return;
        }

        if ($this->record->is_on_tap === null) {
            $this->record->is_on_tap = false;
        }

        if ($this->record->is_available === null) {
            $this->record->is_available = true;
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $failedRowsCount = $import->getFailedRowsCount();

        if ($failedRowsCount === 0) {
            return "Import terminé : {$import->successful_rows} lignes importées.";
        }

        return "Import partiel : {$import->successful_rows} lignes importées, {$failedRowsCount} en erreur.";
    }

    private static function normalizeColor($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = Str::of((string) $value)->lower()->ascii()->trim();

        foreach (BeerColor::cases() as $color) {
            if ($normalized === Str::of($color->value)->lower()->ascii()) {
                return $color->value;
            }

            if ($normalized === Str::of($color->label())->lower()->ascii()) {
                return $color->value;
            }
        }

        return (string) $value;
    }

    private static function normalizeAbvX10($value): ?int
    {
        $abv = self::parseNumeric($value);
        if ($abv === null) {
            return null;
        }

        return $abv > 20 ? (int) round($abv) : (int) round($abv * 10);
    }

    private static function normalizeInt($value): ?int
    {
        $number = self::parseNumeric($value);

        return $number === null ? null : (int) round($number);
    }

    private static function normalizePrice($value): ?int
    {
        $number = self::parseNumeric($value);

        return $number === null ? null : (int) round($number * 100);
    }

    private static function normalizeBoolean($value): ?bool
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        $normalized = Str::of((string) $value)->lower()->ascii()->trim();

        $truthy = ['1', 'oui', 'yes', 'true', 'vrai', 'x'];
        $falsy = ['0', 'non', 'no', 'false', 'faux'];

        if (in_array($normalized, $truthy, true)) {
            return true;
        }

        if (in_array($normalized, $falsy, true)) {
            return false;
        }

        return null;
    }

    private static function parseNumeric($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = preg_replace('/[^0-9,\.\-]+/', '', (string) $value);
        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float) $value : null;
    }
}
