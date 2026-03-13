<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\OrderItem;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesByCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Fatturato per Categoria (ultimi 6 mesi)';

    protected static ?int $sort = 11;

    protected ?string $maxHeight = '360px';

    protected function getData(): array
    {
        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->limit(6)
            ->get();

        $labels = [];
        for ($i = 5; $i >= 0; $i--) {
            $labels[] = Carbon::now()->subMonths($i)->translatedFormat('M Y');
        }

        $palette = [
            'rgb(59, 130, 246)',
            'rgb(16, 185, 129)',
            'rgb(245, 158, 11)',
            'rgb(239, 68, 68)',
            'rgb(139, 92, 246)',
            'rgb(6, 182, 212)',
        ];

        $datasets = [];
        foreach ($categories as $idx => $cat) {
            $data = [];
            for ($i = 5; $i >= 0; $i--) {
                $start = Carbon::now()->subMonths($i)->startOfMonth();
                $end   = $start->copy()->endOfMonth();
                $revenue = OrderItem::whereHas('product.categories', fn ($q) => $q->where('categories.id', $cat->id))
                    ->whereHas('order', fn ($q) => $q->whereBetween('created_at', [$start, $end])->where('status', '!=', 'cancelled'))
                    ->sum('subtotal');
                $data[] = round((float) $revenue, 2);
            }
            $color = $palette[$idx % count($palette)];
            $datasets[] = [
                'label' => $cat->name,
                'data' => $data,
                'borderColor' => $color,
                'backgroundColor' => str_replace('rgb(', 'rgba(', str_replace(')', ', 0.08)', $color)),
                'borderWidth' => 2,
                'tension' => 0.4,
                'fill' => false,
                'pointRadius' => 4,
            ];
        }

        return ['datasets' => $datasets, 'labels' => $labels];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'bottom'],
                'tooltip' => [
                    'callbacks' => [
                        'label' => RawJs::make("function(c) { return ' ' + c.dataset.label + ': € ' + c.parsed.y.toLocaleString('it-IT', {minimumFractionDigits: 2}); }"),
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => RawJs::make("function(v) { return '€ ' + v.toLocaleString('it-IT'); }"),
                    ],
                ],
            ],
        ];
    }
}
