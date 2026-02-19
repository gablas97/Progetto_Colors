<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Review;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        
        $startThisMonth = now()->startOfMonth();
        $endThisMonth   = now()->endOfMonth();

        $totalRevenueThisMonth = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startThisMonth, $endThisMonth])
            ->sum('total');

        $startLastMonth = now()->subMonth()->startOfMonth();
        $endLastMonth   = now()->subMonth()->endOfMonth();

        $totalRevenueLastMonth = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startLastMonth, $endLastMonth])
            ->sum('total');

        $revenueChange = $totalRevenueLastMonth > 0 
            ? (($totalRevenueThisMonth - $totalRevenueLastMonth) / $totalRevenueLastMonth) * 100 
            : 0;

        $totalOrders = Order::count();
        $ordersThisMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $ordersLastMonth = Order::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        $ordersChange = $ordersLastMonth > 0 
            ? (($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100 
            : 0;

        $lowStockProducts = Product::where('manage_stock', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->count();

        $outOfStockProducts = Product::where('manage_stock', true)
            ->where('stock_quantity', 0)
            ->count();

        $averageRating = Review::where('is_approved', true)->avg('rating');

        return [
            Stat::make('Fatturato Totale', '€' . number_format($totalRevenue, 2))
                ->description('Per un totale di ' . number_format($totalOrders) . ' ordini')
                ->color('success'),

            Stat::make('Fatturato Mensile', '€' . number_format($totalRevenueThisMonth, 2))
                ->description(
                    ($revenueChange >= 0 ? '+' : '') . number_format($revenueChange, 1) . '% rispetto al mese scorso'
                )
                ->descriptionIcon(
                    $revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down'
                )
                ->color($revenueChange >= 0 ? 'success' : 'danger'),

            Stat::make('Valutazione Media', number_format($averageRating, 2) . ' ⭐')
                ->description('Basata su ' . Review::where('is_approved', true)->count() . ' recensioni')
                ->descriptionIcon('heroicon-m-star')
                ->color('success'),

            Stat::make('Prodotti in Esaurimento', $lowStockProducts)
                ->description('Stock basso')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockProducts > 0 ? 'warning' : 'success')
                ->url(route('filament.admin.resources.products.index', ['activeTab' => 'low_stock'])),

            Stat::make('Prodotti Esauriti', $outOfStockProducts)
                ->description('Da riordinare')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($outOfStockProducts > 0 ? 'danger' : 'success')
                ->url(route('filament.admin.resources.products.index', ['activeTab' => 'out_of_stock'])),
        ];
    }
}
