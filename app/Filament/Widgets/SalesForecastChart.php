<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesForecastChart extends ChartWidget
{
    protected ?string $heading = 'Andamento e Previsione Fatturato (6 mesi effettivi + 3 previsione)';

    protected static ?int $sort = 10;

    protected ?string $maxHeight = '380px';

    protected function getData(): array
    {
        $labels  = [];
        $actual  = [];
        $forecast = [];

        for ($i = 5; $i >= 0; $i--) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end   = $start->copy()->endOfMonth();

            $revenue = Order::whereBetween('created_at', [$start, $end])
                ->where('status', '!=', 'cancelled')
                ->sum('total');

            $labels[]   = $start->translatedFormat('M Y');
            $actual[]   = round((float) $revenue, 2);
            $forecast[] = null;
        }

        $lastThree = array_slice($actual, -3);
        $avg       = count($lastThree) ? array_sum($lastThree) / count($lastThree) : 0;
        $trend     = count($lastThree) >= 2
            ? ($lastThree[count($lastThree) - 1] - $lastThree[0]) / count($lastThree)
            : 0;

        for ($i = 1; $i <= 3; $i++) {
            $labels[]   = Carbon::now()->addMonths($i)->translatedFormat('M Y');
            $actual[]   = null;
            $forecast[] = round(max(0, $avg + ($trend * $i)), 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Fatturato Effettivo',
                    'data' => $actual,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.08)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                    'spanGaps' => false,
                    'pointBackgroundColor' => 'rgb(16, 185, 129)',
                    'pointRadius' => 5,
                ],
                [
                    'label' => 'Previsione',
                    'data' => $forecast,
                    'borderColor' => 'rgb(99, 102, 241)',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.06)',
                    'borderWidth' => 2,
                    'borderDash' => [6, 4],
                    'fill' => true,
                    'tension' => 0.4,
                    'spanGaps' => false,
                    'pointBackgroundColor' => 'rgb(99, 102, 241)',
                    'pointStyle' => 'triangle',
                    'pointRadius' => 5,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'top'],
                'tooltip' => [
                    'callbacks' => [
                        'label' => RawJs::make(<<<'JS'
                            function(context) {
                                if (context.parsed.y === null) return null;
                                return ' ' + context.dataset.label + ': € ' + context.parsed.y.toLocaleString('it-IT', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                        JS),
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
