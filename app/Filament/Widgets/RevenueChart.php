<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Flowframe\Trend\Trend;
use Illuminate\Support\Carbon;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Andamento Fatturato';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected ?string $maxHeight = '400px';

    protected ?string $pollingInterval = null;

    public ?string $filter = 'month';

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

        return [
            'datasets' => [
                [
                    'label' => 'Fatturato (€)',
                    'data' => $trend->map(fn ($value) => round($value->aggregate, 2)),
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.15)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $trend->map(fn ($value) => $this->formatLabel($value->date)),
        ];
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

}