<?php

namespace App\Services;

use App\Enums\BeerColor;
use App\Models\Bar;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BeerImportService
{
    public function importFromPath(Bar $bar, string $filePath, array $mappingPayload = []): array
    {
        $handle = fopen($filePath, 'r');
        if (! $handle) {
            return [
                'status' => 'error',
                'message' => 'Impossible de lire le fichier.',
            ];
        }

        $delimiter = $this->detectDelimiter($handle);
        rewind($handle);

        $headerRow = fgetcsv($handle, 0, $delimiter);
        if (! $headerRow) {
            fclose($handle);

            return [
                'status' => 'error',
                'message' => 'Le fichier est vide.',
            ];
        }

        $headerMap = $mappingPayload !== []
            ? $this->mapHeadersWithMapping($headerRow, $mappingPayload)
            : $this->mapHeaders($headerRow);
        $missingHeaders = $this->missingRequiredHeaders($headerMap);
        if ($missingHeaders !== []) {
            fclose($handle);

            return [
                'status' => 'error',
                'message' => 'Colonnes obligatoires manquantes.',
                'missingHeaders' => $missingHeaders,
            ];
        }

        $imported = 0;
        $failed = [];
        $total = 0;
        $rowNumber = 1;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rowNumber++;

            if ($this->rowIsEmpty($row)) {
                continue;
            }

            $total++;
            $rowData = $this->extractRowData($headerMap, $row);
            $normalized = $this->normalizeImportRow($rowData);
            $validator = Validator::make($normalized, $this->importRules());

            if ($validator->fails()) {
                $failed[] = [
                    'row' => $rowNumber,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            $bar->beers()->create($validator->validated());
            $imported++;
        }

        fclose($handle);

        $status = $failed !== [] ? 'warning' : 'success';
        $message = $failed !== []
            ? 'Import partiel : certaines lignes nécessitent une correction.'
            : 'Import terminé avec succès.';

        return [
            'status' => $status,
            'message' => $message,
            'imported' => $imported,
            'total' => $total,
            'failed' => array_slice($failed, 0, 10),
            'failedCount' => count($failed),
        ];
    }

    private function detectDelimiter($handle): string
    {
        $sample = fgets($handle) ?: '';
        $commaCount = substr_count($sample, ',');
        $semicolonCount = substr_count($sample, ';');

        return $semicolonCount >= $commaCount ? ';' : ',';
    }

    private function mapHeaders(array $headerRow): array
    {
        $aliases = $this->headerAliases();
        $mapped = [];

        foreach ($headerRow as $index => $header) {
            if (! is_string($header)) {
                continue;
            }

            $normalized = $this->normalizeHeader($header);
            foreach ($aliases as $field => $labels) {
                foreach ($labels as $label) {
                    if ($normalized === $this->normalizeHeader($label)) {
                        $mapped[$field] = $index;
                        break 2;
                    }
                }
            }
        }

        return $mapped;
    }

    private function mapHeadersWithMapping(array $headerRow, array $mappingPayload): array
    {
        $mapped = [];

        foreach ($mappingPayload as $field => $selectedHeader) {
            if (! is_string($selectedHeader) || trim($selectedHeader) === '') {
                continue;
            }

            $selectedHeader = trim($selectedHeader);

            if (is_numeric($selectedHeader)) {
                $index = (int) $selectedHeader;
                if (array_key_exists($index, $headerRow)) {
                    $mapped[$field] = $index;
                }
                continue;
            }

            foreach ($headerRow as $index => $header) {
                if (! is_string($header)) {
                    continue;
                }

                if ($this->normalizeHeader($header) === $this->normalizeHeader($selectedHeader)) {
                    $mapped[$field] = $index;
                    break;
                }
            }
        }

        return $mapped;
    }

    private function missingRequiredHeaders(array $headerMap): array
    {
        $missing = [];

        foreach (['name', 'style', 'color'] as $required) {
            if (! array_key_exists($required, $headerMap)) {
                $missing[] = $required;
            }
        }

        if (! array_key_exists('abv', $headerMap) && ! array_key_exists('abv_x10', $headerMap)) {
            $missing[] = 'abv';
        }

        return $missing;
    }

    private function extractRowData(array $headerMap, array $row): array
    {
        $data = [];

        foreach ($headerMap as $field => $index) {
            $data[$field] = $row[$index] ?? null;
        }

        return $data;
    }

    private function importRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'brewery' => ['nullable', 'string', 'max:255'],
            'style' => ['required', 'string', 'max:255'],
            'color' => ['required', Rule::in(array_map(fn (BeerColor $color) => $color->value, BeerColor::cases()))],
            'abv_x10' => ['required', 'integer', 'min:0', 'max:200'],
            'ibu' => ['nullable', 'integer', 'min:0', 'max:120'],
            'description' => ['nullable', 'string'],
            'is_on_tap' => ['boolean'],
            'is_available' => ['boolean'],
            'price' => ['nullable', 'integer', 'min:0'],
        ];
    }

    private function normalizeImportRow(array $row): array
    {
        $normalized = [
            'name' => $this->stringOrNull($row['name'] ?? null),
            'brewery' => $this->stringOrNull($row['brewery'] ?? null),
            'style' => $this->stringOrNull($row['style'] ?? null),
            'color' => $this->normalizeColor($row['color'] ?? null),
            'abv_x10' => $this->normalizeAbvX10($row['abv_x10'] ?? null, $row['abv'] ?? null),
            'ibu' => $this->normalizeInt($row['ibu'] ?? null),
            'description' => $this->stringOrNull($row['description'] ?? null),
            'is_on_tap' => $this->normalizeBoolean($row['is_on_tap'] ?? null),
            'is_available' => $this->normalizeBoolean($row['is_available'] ?? null),
            'price' => $this->normalizePrice($row['price'] ?? null),
        ];

        return array_filter($normalized, static fn ($value) => $value !== null);
    }

    private function normalizeHeader(string $header): string
    {
        $value = Str::of($header)
            ->lower()
            ->ascii();

        $value = preg_replace('/[^a-z0-9]+/', ' ', $value);

        return trim($value);
    }

    private function headerAliases(): array
    {
        return [
            'name' => ['name', 'nom', 'biere', 'bière', 'nom biere', 'nom bière'],
            'brewery' => ['brewery', 'brasserie', 'producteur', 'brasseur'],
            'style' => ['style', 'type'],
            'color' => ['color', 'couleur', 'robe'],
            'abv' => ['abv', 'alcool', 'degre', 'degré', 'taux', 'vol'],
            'abv_x10' => ['abv_x10', 'abv x10', 'abv10', 'degre x10', 'degré x10'],
            'ibu' => ['ibu', 'amertume'],
            'description' => ['description', 'notes'],
            'is_on_tap' => ['is_on_tap', 'pression', 'draft', 'on tap', 'tirage'],
            'is_available' => ['is_available', 'disponible', 'dispo', 'available', 'actif'],
            'price' => ['price', 'prix', 'tarif', 'prix ttc'],
        ];
    }

    private function normalizeColor($value): ?string
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

    private function normalizeAbvX10($abvX10Raw, $abvRaw): ?int
    {
        $abvX10 = $this->parseNumeric($abvX10Raw);
        if ($abvX10 !== null) {
            return (int) round($abvX10);
        }

        $abv = $this->parseNumeric($abvRaw);
        if ($abv === null) {
            return null;
        }

        return $abv > 20 ? (int) round($abv) : (int) round($abv * 10);
    }

    private function normalizeInt($value): ?int
    {
        $number = $this->parseNumeric($value);

        return $number === null ? null : (int) round($number);
    }

    private function normalizePrice($value): ?int
    {
        $number = $this->parseNumeric($value);

        return $number === null ? null : (int) round($number * 100);
    }

    private function normalizeBoolean($value): ?bool
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

    private function stringOrNull($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function parseNumeric($value): ?float
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

    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }
}

