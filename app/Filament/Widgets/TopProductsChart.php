<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Str;

class TopProductsChart extends ChartWidget
{
    protected ?string $heading = 'Top 10 Prodotti per Vendite';

    protected static ?int $sort = 3;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $products = Product::where('is_active', true)
            ->where('sales_count', '>', 0)
            ->orderBy('sales_count', 'desc')
            ->limit(10)
            ->get(['name', 'sales_count']);

        if ($products->isEmpty()) {
            return ['datasets' => [['label' => 'Vendite', 'data' => []]], 'labels' => []];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Unità Vendute',
                    'data' => $products->pluck('sales_count')->toArray(),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(6, 182, 212, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(20, 184, 166, 0.8)',
                        'rgba(99, 102, 241, 0.8)',
                    ],
                    'borderWidth' => 0,
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $products->map(fn ($p) => Str::limit($p->name, 24))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'callbacks' => [
                        'label' => \Filament\Support\RawJs::make("function(c) { return ' ' + c.parsed.x + ' unità vendute'; }"),
                    ],
                ],
            ],
            'scales' => [
                'x' => ['beginAtZero' => true, 'ticks' => ['stepSize' => 1]],
                'y' => ['ticks' => ['font' => ['size' => 11]]],
            ],
        ];
    }
}
