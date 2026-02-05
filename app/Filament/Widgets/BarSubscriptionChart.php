<?php

namespace App\Filament\Widgets;

use App\Models\Bar;
use Alkoumi\FilamentGoogleCharts\Widgets\PieChartWidget;

class BarSubscriptionChart extends PieChartWidget
{
    protected static ?string $heading = 'Répartition des bars';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 1;

    protected static ?array $options = [
        'pieHole' => 0.4,
        'legend' => ['position' => 'right'],
        'height' => 320,
    ];

    protected function getData(): array
    {
        $bars = Bar::with('users')->get();

        $subscribed = 0;
        $trial = 0;
        $inactive = 0;

        foreach ($bars as $bar) {
            $status = $bar->subscriptionStatus();
            if ($status === 'active') {
                $subscribed++;
                continue;
            }

            if ($status === 'trial') {
                $trial++;
                continue;
            }

            $inactive++;
        }

        return [
            ['Statut', 'Bars'],
            ['Abonnés', $subscribed],
            ['Essai', $trial],
            ['Non abonnés', $inactive],
        ];
    }
}

