<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\WarehouseMovement;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class DashboardMagazzino extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';
    protected static string|UnitEnum|null $navigationGroup = 'Magazzino';
    protected static ?string $navigationLabel = 'Dashboard Magazzino';
    protected static ?int $navigationSort = 6;
    protected static ?string $title = 'Dashboard Magazzino';
    protected string $view = 'filament.pages.dashboard-magazzino';

    public function getViewData(): array
    {
        $totalProducts = Product::where('is_active', true)->where('manage_stock', true)->count();
        $totalItems = Product::where('is_active', true)->where('manage_stock', true)->sum('stock_quantity');

        $warehouseValue = Product::where('is_active', true)->where('manage_stock', true)
            ->selectRaw('SUM(stock_quantity * COALESCE(cost, price)) as total')->value('total') ?? 0;

        $warehouseRetailValue = Product::where('is_active', true)->where('manage_stock', true)
            ->selectRaw('SUM(stock_quantity * price) as total')->value('total') ?? 0;

        $outOfStockCount = Product::where('manage_stock', true)->where('stock_quantity', '<=', 0)->where('is_active', true)->count();
        $lowStockCount = Product::where('manage_stock', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0)->where('is_active', true)->count();
        $healthyStockCount = $totalProducts - $outOfStockCount - $lowStockCount;

        // Movimenti ultimi 30 giorni
        $last30Days = Carbon::now()->subDays(30);
        $movementsLast30 = WarehouseMovement::where('created_at', '>=', $last30Days)
            ->select('type', DB::raw('COUNT(*) as count'), DB::raw('SUM(ABS(quantity)) as total_qty'))
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        // Movimenti per giorno (ultimi 14 giorni)
        $dailyMovements = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $carichi = WarehouseMovement::whereDate('created_at', $date)->where('type', 'carico')->sum('quantity');
            $scarichi = WarehouseMovement::whereDate('created_at', $date)->where('type', 'scarico')->sum(DB::raw('ABS(quantity)'));
            $dailyMovements[] = [
                'date' => $date->format('d/m'),
                'carichi' => abs($carichi),
                'scarichi' => abs($scarichi),
            ];
        }

        // Top prodotti per rotazione (indice di rotazione = vendite / giacenza media)
        $topRotation = Product::where('is_active', true)
            ->where('manage_stock', true)
            ->where('stock_quantity', '>', 0)
            ->where('sales_count', '>', 0)
            ->orderByRaw('sales_count / GREATEST(stock_quantity, 1) DESC')
            ->limit(10)
            ->get()
            ->map(fn ($p) => [
                'name' => $p->name,
                'sku' => $p->sku,
                'stock' => $p->stock_quantity,
                'sales' => $p->sales_count,
                'rotation' => $p->stock_quantity > 0 ? round($p->sales_count / $p->stock_quantity, 2) : 0,
            ]);

        // Valore stock per categoria
        $stockByCategory = DB::table('products')
            ->join('category_product', 'products.id', '=', 'category_product.product_id')
            ->join('categories', 'categories.id', '=', 'category_product.category_id')
            ->where('products.is_active', true)
            ->where('products.manage_stock', true)
            ->whereNull('categories.parent_id')
            ->groupBy('categories.name')
            ->select('categories.name', DB::raw('SUM(products.stock_quantity) as total_qty'), DB::raw('SUM(products.stock_quantity * COALESCE(products.cost, products.price)) as total_value'))
            ->get();

        return compact(
            'totalProducts', 'totalItems', 'warehouseValue', 'warehouseRetailValue',
            'outOfStockCount', 'lowStockCount', 'healthyStockCount',
            'movementsLast30', 'dailyMovements', 'topRotation', 'stockByCategory'
        );
    }
}
