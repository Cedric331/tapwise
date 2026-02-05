<?php

namespace App\Filament\Widgets;

use Alkoumi\FilamentGoogleCharts\Widgets\ColumnChartWidget;
use App\Models\Bar;

class BarScansChart extends ColumnChartWidget
{
    protected static ?string $heading = 'Scans par bar (Top 10)';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?array $options = [
        'legend' => ['position' => 'none'],
        'height' => 360,
    ];

    protected function getData(): array
    {
        $rows = Bar::query()
            ->orderByDesc('count_scans')
            ->limit(10)
            ->get(['name', 'count_scans'])
            ->map(fn (Bar $bar) => [$bar->name, (int) $bar->count_scans])
            ->all();

        return array_merge([['Bar', 'Scans']], $rows);
    }
}

