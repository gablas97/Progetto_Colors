<?php

namespace App\Filament\Pages;

use BackedEnum;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 1;

    public function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverview::class,
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            \App\Filament\Widgets\RevenueChart::class,
            \App\Filament\Widgets\LatestOrders::class,
            \App\Filament\Widgets\PopularProducts::class,
            \App\Filament\Widgets\LowStockAlert::class,
            \App\Filament\Widgets\RecentReviews::class,
        ];
    }

    public function getColumns(): int
    {
        return 2;
    }
}
