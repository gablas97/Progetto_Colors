<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Flowframe\Trend\Trend;
use Filament\Support\RawJs;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Andamento Fatturato';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected ?string $maxHeight = '400px';

    protected ?string $pollingInterval = null;

    public ?string $filter = 'month';

    protected int $yMax;

    protected function getFilters(): ?array
    {
        return [
            'week' => 'Questa settimana',
            'month' => 'Questo mese',
            'quarter' => 'Questo trimestre',
            'year' => 'Quest\'anno',
        ];
    }

    protected function getData(): array
    {
        $query = Order::query()->where('payment_status', 'paid');

        [$start, $end, $groupBy] = match ($this->filter) {
            'week' => [now()->startOfWeek(), now()->endOfWeek(), 'day'],
            'month' => [now()->startOfMonth(), now()->endOfMonth(), 'day'],
            'quarter' => $this->currentQuarterRange(),
            'year' => [now()->startOfYear(), now()->endOfYear(), 'month'],
            default => [now()->startOfMonth(), now()->endOfMonth(), 'day'],
        };

        $trend = Trend::query($query)
            ->between(start: $start, end: $end)
            ->{"per" . ucfirst($groupBy)}()
            ->sum('total');

        $maxValue = $trend->max(fn (TrendValue $value) => $value->aggregate) ?? 0;

        $this->yMax = $this->calculateYAxisMax($maxValue);

        return [
            'datasets' => [
                [
                    'label' => 'Fatturato (€)',
                    'data' => $trend->map(fn (TrendValue $value) => round($value->aggregate, 2)),
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.15)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $trend->map(fn (TrendValue $value) =>
                $this->formatLabel($value->date)
            ),
        ];
    }

    protected function calculateYAxisMax(float $max): int
    {
        return match ($this->filter) {
            'week' => max(100, ceil($max / 50) * 50),
            'month' => max(300, ceil($max / 100) * 100),
            'quarter' => max(600, ceil($max / 200) * 200),
            'year' => max(1000, ceil($max / 500) * 500),
            default => ceil($max / 100) * 100,
        };
    }
    
    protected function currentQuarterRange(): array
    {
        $month = now()->month;
        $quarter = intdiv($month - 1, 3) + 1;

        $start = Carbon::create(now()->year, (($quarter - 1) * 3) + 1)->startOfMonth();
        $end = (clone $start)->addMonths(2)->endOfMonth();

        return [$start, $end, 'month'];
    }

    protected function formatLabel(string $date): string
    {
        $date = Carbon::parse($date);

        return match ($this->filter) {
            'week' => $date->isoFormat('ddd'),
            'month' => $date->format('d'),
            'quarter', 'year' => $date->isoFormat('MMM'),
            default => $date->format('d/m'),
        };
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => RawJs::make(<<<'JS'
                            function(context) {
                                const value = context.parsed.y || 0;
                                return ' € ' + value.toFixed(2);
                            }
                        JS),
                        'title' => RawJs::make(<<<'JS'
                            function(context) {
                                return context[0].label;
                            }
                        JS),
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'suggestedMax' => RawJs::make('meta.yMax'),
                    'ticks' => [
                        'callback' => RawJs::make(<<<'JS'
                                function (value) {
                                    return '€' + value;
                                }
                                JS
                            ),
                        ],
                ],
            ],
        ];
    }
}