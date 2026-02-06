<?php

namespace App\Filament\Widgets;

use App\Models\Bar;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $bars = Bar::with('users')->get();

        $totalBars = $bars->count();
        $totalScans = $bars->sum('count_scans');
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
            Stat::make('Scans', number_format($totalScans, 0, ',', ' ')),
            Stat::make('Bars', $totalBars),
            Stat::make('Bars abonnés', $subscribed),
            Stat::make('Bars en essai', $trial),
            Stat::make('Bars non abonnés', $inactive),
        ];
    }
}
