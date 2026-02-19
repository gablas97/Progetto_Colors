<?php


namespace App\Filament\Pages;

use BackedEnum;
use UnitEnum;

use App\Models\Order;
use App\Models\Product;
use App\Models\CalendarEvent;
use App\Models\WarehouseMovement;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class Panoramica extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Panoramica';
    protected static string|UnitEnum|null $navigationGroup = 'Generale';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'Panoramica';
    protected string $view = 'filament.pages.panoramica';

    public string $period = 'month';

    public function getViewData(): array
    {
        // === STATISTICHE GENERALI ===
        $warehouseValue = Product::where('is_active', true)
            ->where('manage_stock', true)
            ->selectRaw('SUM(stock_quantity * COALESCE(cost, price)) as total')
            ->value('total') ?? 0;

        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $monthlyOrders = Order::whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', '!=', 'cancelled');
        $monthlyRevenue = (clone $monthlyOrders)->sum('total');
        $monthlyOrderCount = (clone $monthlyOrders)->count();

        $lowStockCount = Product::where('manage_stock', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->where('is_active', true)
            ->count();

        $outOfStockCount = Product::where('manage_stock', true)
            ->where('stock_quantity', '<=', 0)
            ->where('is_active', true)
            ->count();

        $monthlyReturns = Order::whereBetween('return_requested_at', [$monthStart, $monthEnd])
            ->where('return_status', '!=', 'none')
            ->count();

        // === GRAFICI - Fatturato per periodo ===
        $revenueData = $this->getRevenueData();
        $topProductsData = $this->getTopProductsData();

        // === PRODOTTI IN ESAURIMENTO ===
        $lowStockProducts = Product::where('manage_stock', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->where('is_active', true)
            ->with('brand')
            ->orderBy('stock_quantity')
            ->limit(10)
            ->get();

        $outOfStockProducts = Product::where('manage_stock', true)
            ->where('stock_quantity', '<=', 0)
            ->where('is_active', true)
            ->with('brand')
            ->limit(10)
            ->get();

        // === SCADENZE (da Agenda) ===
        $upcomingEvents = CalendarEvent::where('is_completed', false)
            ->where('starts_at', '>=', now())
            ->where('starts_at', '<=', now()->addDays(14))
            ->orderBy('starts_at')
            ->limit(8)
            ->get();

        $overdueEvents = CalendarEvent::where('is_completed', false)
            ->where('starts_at', '<', now())
            ->orderBy('starts_at', 'desc')
            ->limit(5)
            ->get();

        // === MOVIMENTI RECENTI ===
        $recentMovements = WarehouseMovement::with(['product', 'user'])
            ->latest()
            ->limit(10)
            ->get();

        return compact(
            'warehouseValue',
            'monthlyRevenue',
            'monthlyOrderCount',
            'lowStockCount',
            'outOfStockCount',
            'monthlyReturns',
            'revenueData',
            'topProductsData',
            'lowStockProducts',
            'outOfStockProducts',
            'upcomingEvents',
            'overdueEvents',
            'recentMovements'
        );
    }

    protected function getRevenueData(): array
    {
        $data = [];
        $now = Carbon::now();

        switch ($this->period) {
            case 'week':
                for ($i = 6; $i >= 0; $i--) {
                    $date = $now->copy()->subDays($i);
                    $revenue = Order::whereDate('created_at', $date)
                        ->where('status', '!=', 'cancelled')
                        ->sum('total');
                    $data[] = ['label' => $date->format('D d'), 'value' => round($revenue, 2)];
                }
                break;
            case 'month':
                for ($i = 29; $i >= 0; $i -= 3) {
                    $date = $now->copy()->subDays($i);
                    $revenue = Order::whereDate('created_at', $date)
                        ->where('status', '!=', 'cancelled')
                        ->sum('total');
                    $data[] = ['label' => $date->format('d/m'), 'value' => round($revenue, 2)];
                }
                break;
            case 'quarter':
                $quarterStart = $now->copy()->startOfQuarter();
                for ($i = 0; $i < 3; $i++) {
                    $start = $quarterStart->copy()->addMonths($i)->startOfMonth();
                    $end = $start->copy()->endOfMonth();
                    $revenue = Order::whereBetween('created_at', [$start, $end])
                        ->where('status', '!=', 'cancelled')
                        ->sum('total');
                    $data[] = ['label' => $start->translatedFormat('M Y'), 'value' => round($revenue, 2)];
                }
                break;
            case 'year':
                for ($i = 11; $i >= 0; $i--) {
                    $start = $now->copy()->subMonths($i)->startOfMonth();
                    $end = $start->copy()->endOfMonth();
                    $revenue = Order::whereBetween('created_at', [$start, $end])
                        ->where('status', '!=', 'cancelled')
                        ->sum('total');
                    $data[] = ['label' => $start->translatedFormat('M'), 'value' => round($revenue, 2)];
                }
                break;
        }

        return $data;
    }

    protected function getTopProductsData(): array
    {
        return Product::where('is_active', true)
            ->where('sales_count', '>', 0)
            ->orderBy('sales_count', 'desc')
            ->limit(10)
            ->get()
            ->map(fn ($p) => ['name' => $p->name, 'sales' => $p->sales_count, 'revenue' => round($p->sales_count * $p->price, 2)])
            ->toArray();
    }
}
