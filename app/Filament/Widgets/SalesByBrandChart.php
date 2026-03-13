<?php

namespace App\Filament\Widgets;

use App\Models\Brand;
use App\Models\OrderItem;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesByBrandChart extends ChartWidget
{
    protected ?string $heading = 'Fatturato per Marca (ultimi 6 mesi)';

    protected static ?int $sort = 12;

    protected ?string $maxHeight = '360px';

    protected function getData(): array
    {
        $brands = Brand::where('is_active', true)
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit(6)
            ->get();

        $labels = [];
        for ($i = 5; $i >= 0; $i--) {
            $labels[] = Carbon::now()->subMonths($i)->translatedFormat('M Y');
        }

        $palette = [
            'rgb(249, 115, 22)',
            'rgb(6, 182, 212)',
            'rgb(236, 72, 153)',
            'rgb(20, 184, 166)',
            'rgb(99, 102, 241)',
            'rgb(245, 158, 11)',
        ];

        $datasets = [];
        foreach ($brands as $idx => $brand) {
            $data = [];
            for ($i = 5; $i >= 0; $i--) {
                $start = Carbon::now()->subMonths($i)->startOfMonth();
                $end   = $start->copy()->endOfMonth();
                $revenue = OrderItem::whereHas('product', fn ($q) => $q->where('brand_id', $brand->id))
                    ->whereHas('order', fn ($q) => $q->whereBetween('created_at', [$start, $end])->where('status', '!=', 'cancelled'))
                    ->sum('subtotal');
                $data[] = round((float) $revenue, 2);
            }
            $color = $palette[$idx % count($palette)];
            $datasets[] = [
                'label' => $brand->name,
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
