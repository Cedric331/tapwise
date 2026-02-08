<?php

namespace App\Services;

use App\Enums\WineColor;
use App\Models\Bar;
use App\Support\WineFoodPairings;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WineImportService
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

            $bar->wines()->create($validator->validated());
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

        foreach (['name', 'color'] as $required) {
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
            'color' => ['required', Rule::in(array_map(fn (WineColor $color) => $color->value, WineColor::cases()))],
            'grape' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'food_pairings' => ['nullable', 'array'],
            'food_pairings.*' => [Rule::in(WineFoodPairings::ids())],
            'abv_x10' => ['required', 'integer', 'min:0', 'max:250'],
            'description' => ['nullable', 'string'],
            'is_available' => ['boolean'],
            'price' => ['nullable', 'integer', 'min:0'],
        ];
    }

    private function normalizeImportRow(array $rowData): array
    {
        $normalized = [
            'name' => $rowData['name'] ?? null,
            'color' => $this->normalizeColor($rowData['color'] ?? null),
            'grape' => $this->normalizeValue($rowData['grape'] ?? null),
            'region' => $this->normalizeValue($rowData['region'] ?? null),
            'food_pairings' => WineFoodPairings::normalizeInput($rowData['food_pairings'] ?? null),
            'description' => $this->normalizeValue($rowData['description'] ?? null),
            'is_available' => $this->normalizeBoolean($rowData['is_available'] ?? null, true),
            'price' => $this->normalizePrice($rowData['price'] ?? null),
        ];

        $abv = $rowData['abv_x10'] ?? $rowData['abv'] ?? null;
        $normalized['abv_x10'] = $this->normalizeAbv($abv);

        return $normalized;
    }

    private function normalizeColor(?string $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $normalized = Str::of($value)->lower()->trim();

        $aliases = [
            'rouge' => 'red',
            'red' => 'red',
            'blanc' => 'white',
            'white' => 'white',
            'rose' => 'rose',
            'rosé' => 'rose',
        ];

        return $aliases[$normalized->toString()] ?? $normalized->toString();
    }

    private function normalizeAbv($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $numeric = (float) str_replace(',', '.', (string) $value);
        if ($numeric <= 0) {
            return 0;
        }

        return (int) round($numeric * 10);
    }

    private function normalizePrice($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $numeric = (float) str_replace(',', '.', (string) $value);
        if ($numeric < 0) {
            return null;
        }

        return (int) round($numeric * 100);
    }

    private function normalizeBoolean($value, bool $default = false): bool
    {
        if ($value === null || $value === '') {
            return $default;
        }

        $normalized = Str::of((string) $value)->lower()->trim()->toString();

        return in_array($normalized, ['1', 'true', 'vrai', 'oui', 'yes'], true);
    }

    private function normalizeValue($value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function normalizeHeader(string $header): string
    {
        return Str::of($header)->lower()->trim()->replace([' ', '-', '_'], '')->toString();
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function headerAliases(): array
    {
        return [
            'name' => ['nom', 'name', 'vin'],
            'color' => ['couleur', 'color'],
            'grape' => ['cepage', 'cépage', 'grape', 'variete', 'variété'],
            'region' => ['region', 'région', 'appellation'],
            'food_pairings' => ['accords', 'accords_mets', 'accords_mets_vin', 'plats', 'food', 'food_pairings'],
            'abv' => ['abv', 'alcool', 'degree', 'degre', 'degré'],
            'abv_x10' => ['abv_x10', 'abv10'],
            'description' => ['description', 'notes'],
            'is_available' => ['disponible', 'available', 'is_available'],
            'price' => ['prix', 'price', 'tarif'],
        ];
    }
}

