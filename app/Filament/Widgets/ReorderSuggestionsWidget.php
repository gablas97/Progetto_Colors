<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use App\Models\Product;
use Filament\Widgets\Widget;

class ReorderSuggestionsWidget extends Widget
{
    protected string $view = 'filament.widgets.reorder-suggestions';

    protected static ?int $sort = 14;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $products = Product::where('is_active', true)
            ->where('manage_stock', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->with('brand')
            ->get();

        $suggestions = [];

        foreach ($products as $product) {
            $salesLast90Days = OrderItem::where('product_id', $product->id)
                ->whereHas('order', fn ($q) => $q
                    ->where('created_at', '>=', now()->subDays(90))
                    ->where('status', '!=', 'cancelled')
                )
                ->sum('quantity');

            $dailySalesRate = $salesLast90Days / 90;
            $daysOfStock    = $dailySalesRate > 0 ? round($product->stock_quantity / $dailySalesRate) : 999;
            $suggestedQty   = max(0, (int) ceil($dailySalesRate * 30) - $product->stock_quantity);

            $urgency = match (true) {
                $product->stock_quantity <= 0 => 'critica',
                $daysOfStock <= 7             => 'alta',
                $daysOfStock <= 14            => 'media',
                default                       => 'bassa',
            };

            if ($suggestedQty > 0 || $product->stock_quantity <= 0) {
                $suggestions[] = [
                    'product'          => $product->name,
                    'sku'              => $product->sku,
                    'brand'            => $product->brand?->name ?? '—',
                    'current_stock'    => $product->stock_quantity,
                    'threshold'        => $product->low_stock_threshold,
                    'daily_sales_rate' => round($dailySalesRate, 2),
                    'days_of_stock'    => $daysOfStock,
                    'suggested_qty'    => $suggestedQty,
                    'urgency'          => $urgency,
                    'estimated_cost'   => round($suggestedQty * ($product->cost ?? $product->price), 2),
                ];
            }
        }

        usort($suggestions, function ($a, $b) {
            $order = ['critica' => 0, 'alta' => 1, 'media' => 2, 'bassa' => 3];

            return ($order[$a['urgency']] ?? 4) <=> ($order[$b['urgency']] ?? 4);
        });

        return ['suggestions' => $suggestions];
    }
}
