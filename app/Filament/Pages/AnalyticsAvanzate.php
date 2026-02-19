<?php

namespace App\Filament\Pages;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class AnalyticsAvanzate extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationLabel = 'Analytics Avanzate';
    protected static string|UnitEnum|null $navigationGroup = 'Generale';
    protected static ?int $navigationSort = 2;
    protected static ?string $title = 'Analytics Avanzate';
    protected string $view = 'filament.pages.analytics-avanzate';

    public function getViewData(): array
    {
        return [
            'abcAnalysis' => $this->getAbcAnalysis(),
            'salesTrendByCategory' => $this->getSalesTrendByCategory(),
            'salesTrendByBrand' => $this->getSalesTrendByBrand(),
            'performanceComparison' => $this->getPerformanceComparison(),
            'reorderSuggestions' => $this->getReorderSuggestions(),
            'salesForecast' => $this->getSalesForecast(),
        ];
    }

    /**
     * Analisi ABC basata sul principio di Pareto (80/20).
     * Classifica i prodotti in A (top 80% fatturato), B (successivo 15%), C (restante 5%).
     */
    protected function getAbcAnalysis(): array
    {
        $products = OrderItem::select('product_id', DB::raw('SUM(subtotal) as total_revenue'), DB::raw('SUM(quantity) as total_quantity'))
            ->whereHas('order', fn ($q) => $q->where('status', '!=', 'cancelled'))
            ->groupBy('product_id')
            ->orderBy('total_revenue', 'desc')
            ->with('product:id,name,sku,price,cost')
            ->get();

        $totalRevenue = $products->sum('total_revenue');
        if ($totalRevenue == 0) return ['A' => [], 'B' => [], 'C' => []];

        $cumulativeRevenue = 0;
        $result = ['A' => [], 'B' => [], 'C' => []];

        foreach ($products as $item) {
            $cumulativeRevenue += $item->total_revenue;
            $percentage = ($cumulativeRevenue / $totalRevenue) * 100;

            $category = $percentage <= 80 ? 'A' : ($percentage <= 95 ? 'B' : 'C');

            $result[$category][] = [
                'product' => $item->product?->name ?? 'N/A',
                'sku' => $item->product?->sku ?? '',
                'revenue' => round($item->total_revenue, 2),
                'quantity' => $item->total_quantity,
                'percentage' => round(($item->total_revenue / $totalRevenue) * 100, 2),
                'cumulative' => round($percentage, 2),
            ];
        }

        return $result;
    }

    /**
     * Trend vendite per categoria negli ultimi 6 mesi.
     */
    protected function getSalesTrendByCategory(): array
    {
        $categories = Category::whereNull('parent_id')->where('is_active', true)->get();
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $monthLabel = $start->translatedFormat('M Y');

            $monthData = ['month' => $monthLabel];

            foreach ($categories as $cat) {
                $revenue = OrderItem::whereHas('product.categories', fn ($q) => $q->where('categories.id', $cat->id))
                    ->whereHas('order', fn ($q) => $q->whereBetween('created_at', [$start, $end])->where('status', '!=', 'cancelled'))
                    ->sum('subtotal');
                $monthData[$cat->name] = round($revenue, 2);
            }

            $data[] = $monthData;
        }

        return $data;
    }

    /**
     * Trend vendite per marca negli ultimi 6 mesi.
     */
    protected function getSalesTrendByBrand(): array
    {
        $topBrands = Brand::where('is_active', true)
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit(5)
            ->get();

        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end = $start->copy()->endOfMonth();

            $monthData = ['month' => $start->translatedFormat('M Y')];

            foreach ($topBrands as $brand) {
                $revenue = OrderItem::whereHas('product', fn ($q) => $q->where('brand_id', $brand->id))
                    ->whereHas('order', fn ($q) => $q->whereBetween('created_at', [$start, $end])->where('status', '!=', 'cancelled'))
                    ->sum('subtotal');
                $monthData[$brand->name] = round($revenue, 2);
            }

            $data[] = $monthData;
        }

        return $data;
    }

    /**
     * Confronto performance: questo mese vs mese precedente vs stesso mese anno precedente.
     */
    protected function getPerformanceComparison(): array
    {
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        $prevMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $prevMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $sameMonthLastYearStart = Carbon::now()->subYear()->startOfMonth();
        $sameMonthLastYearEnd = Carbon::now()->subYear()->endOfMonth();

        $getMetrics = function ($start, $end) {
            $orders = Order::whereBetween('created_at', [$start, $end])->where('status', '!=', 'cancelled');
            return [
                'revenue' => round((clone $orders)->sum('total'), 2),
                'orders' => (clone $orders)->count(),
                'avg_order' => round((clone $orders)->avg('total') ?? 0, 2),
                'returns' => Order::whereBetween('return_requested_at', [$start, $end])
                    ->where('return_status', '!=', 'none')->count(),
            ];
        };

        return [
            'current_month' => $getMetrics($currentMonthStart, $currentMonthEnd),
            'previous_month' => $getMetrics($prevMonthStart, $prevMonthEnd),
            'same_month_last_year' => $getMetrics($sameMonthLastYearStart, $sameMonthLastYearEnd),
        ];
    }

    /**
     * Suggerimenti di riordino intelligente basati sulla velocità di vendita.
     */
    protected function getReorderSuggestions(): array
    {
        $products = Product::where('is_active', true)
            ->where('manage_stock', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->with('brand')
            ->get();

        $suggestions = [];

        foreach ($products as $product) {
            // Calcola velocità di vendita media (ultimi 90 giorni)
            $salesLast90Days = OrderItem::where('product_id', $product->id)
                ->whereHas('order', fn ($q) => $q->where('created_at', '>=', now()->subDays(90))->where('status', '!=', 'cancelled'))
                ->sum('quantity');

            $dailySalesRate = $salesLast90Days / 90;
            $daysOfStock = $dailySalesRate > 0 ? round($product->stock_quantity / $dailySalesRate) : 999;

            // Quantità suggerita: copertura per 30 giorni
            $suggestedQty = max(0, ceil($dailySalesRate * 30) - $product->stock_quantity);

            // Urgenza basata sui giorni di stock rimanenti
            $urgency = match (true) {
                $product->stock_quantity <= 0 => 'critica',
                $daysOfStock <= 7 => 'alta',
                $daysOfStock <= 14 => 'media',
                default => 'bassa',
            };

            if ($suggestedQty > 0 || $product->stock_quantity <= 0) {
                $suggestions[] = [
                    'product' => $product->name,
                    'sku' => $product->sku,
                    'brand' => $product->brand?->name ?? '-',
                    'current_stock' => $product->stock_quantity,
                    'threshold' => $product->low_stock_threshold,
                    'daily_sales_rate' => round($dailySalesRate, 2),
                    'days_of_stock' => $daysOfStock,
                    'suggested_qty' => $suggestedQty,
                    'urgency' => $urgency,
                    'estimated_cost' => round($suggestedQty * ($product->cost ?? $product->price), 2),
                ];
            }
        }

        // Ordina per urgenza
        usort($suggestions, function ($a, $b) {
            $order = ['critica' => 0, 'alta' => 1, 'media' => 2, 'bassa' => 3];
            return ($order[$a['urgency']] ?? 4) <=> ($order[$b['urgency']] ?? 4);
        });

        return $suggestions;
    }

    /**
     * Previsione vendite semplice basata sulla media mobile.
     */
    protected function getSalesForecast(): array
    {
        $data = [];
        // Ultimi 6 mesi effettivi
        for ($i = 5; $i >= 0; $i--) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $revenue = Order::whereBetween('created_at', [$start, $end])
                ->where('status', '!=', 'cancelled')
                ->sum('total');
            $data[] = [
                'month' => $start->translatedFormat('M Y'),
                'actual' => round($revenue, 2),
                'forecast' => null,
                'type' => 'actual',
            ];
        }

        // Previsione prossimi 3 mesi (media mobile ultimi 3 mesi con trend)
        $lastThreeMonths = array_slice(array_column($data, 'actual'), -3);
        $avgRevenue = count($lastThreeMonths) > 0 ? array_sum($lastThreeMonths) / count($lastThreeMonths) : 0;

        // Calcola trend (crescita/decrescita)
        $trend = 0;
        if (count($lastThreeMonths) >= 2) {
            $trend = ($lastThreeMonths[count($lastThreeMonths) - 1] - $lastThreeMonths[0]) / count($lastThreeMonths);
        }

        for ($i = 1; $i <= 3; $i++) {
            $forecastMonth = Carbon::now()->addMonths($i);
            $forecast = max(0, $avgRevenue + ($trend * $i));
            $data[] = [
                'month' => $forecastMonth->translatedFormat('M Y'),
                'actual' => null,
                'forecast' => round($forecast, 2),
                'type' => 'forecast',
            ];
        }

        return $data;
    }
}
