<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class PerformanceComparisonWidget extends Widget
{
    protected string $view = 'filament.widgets.performance-comparison';

    protected static ?int $sort = 9;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'data' => [
                'current_month'         => $this->getMetrics(now()->startOfMonth(), now()->endOfMonth()),
                'previous_month'        => $this->getMetrics(now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()),
                'same_month_last_year'  => $this->getMetrics(now()->subYear()->startOfMonth(), now()->subYear()->endOfMonth()),
            ],
            'periodLabels' => [
                'current_month'        => now()->translatedFormat('M Y') . ' (corrente)',
                'previous_month'       => now()->subMonth()->translatedFormat('M Y') . ' (prec.)',
                'same_month_last_year' => now()->subYear()->translatedFormat('M Y') . ' (anno prec.)',
            ],
        ];
    }

    protected function getMetrics(Carbon $start, Carbon $end): array
    {
        $orders = Order::whereBetween('created_at', [$start, $end])->where('status', '!=', 'cancelled');

        return [
            'revenue'   => round((float) (clone $orders)->sum('total'), 2),
            'orders'    => (clone $orders)->count(),
            'avg_order' => round((float) ((clone $orders)->avg('total') ?? 0), 2),
            'returns'   => Order::whereBetween('return_requested_at', [$start, $end])
                ->where('return_status', '!=', 'none')
                ->count(),
        ];
    }
}
