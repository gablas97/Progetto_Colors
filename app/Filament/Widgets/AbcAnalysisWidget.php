<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class AbcAnalysisWidget extends Widget
{
    protected string $view = 'filament.widgets.abc-analysis';

    protected static ?int $sort = 13;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $products = OrderItem::select(
            'product_id',
            DB::raw('SUM(subtotal) as total_revenue'),
            DB::raw('SUM(quantity) as total_quantity')
        )
            ->whereHas('order', fn ($q) => $q->where('status', '!=', 'cancelled'))
            ->groupBy('product_id')
            ->orderBy('total_revenue', 'desc')
            ->with('product:id,name,sku')
            ->get();

        $totalRevenue = $products->sum('total_revenue');

        if ($totalRevenue == 0) {
            return ['abcData' => ['A' => [], 'B' => [], 'C' => []], 'totalRevenue' => 0];
        }

        $cumulativeRevenue = 0;
        $result = ['A' => [], 'B' => [], 'C' => []];

        foreach ($products as $item) {
            $cumulativeRevenue += $item->total_revenue;
            $cumPct  = ($cumulativeRevenue / $totalRevenue) * 100;
            $class   = $cumPct <= 80 ? 'A' : ($cumPct <= 95 ? 'B' : 'C');

            $result[$class][] = [
                'product'    => $item->product?->name ?? 'N/A',
                'sku'        => $item->product?->sku ?? '',
                'revenue'    => round((float) $item->total_revenue, 2),
                'quantity'   => (int) $item->total_quantity,
                'percentage' => round(($item->total_revenue / $totalRevenue) * 100, 2),
                'cumulative' => round($cumPct, 2),
            ];
        }

        return ['abcData' => $result, 'totalRevenue' => (float) $totalRevenue];
    }
}
