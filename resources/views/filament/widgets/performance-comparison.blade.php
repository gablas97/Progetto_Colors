<x-filament-widgets::widget>
    <x-filament::section
        icon="heroicon-o-scale"
        icon-color="info"
        heading="Confronto Performance Mensile"
    >
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="py-3 px-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-40">
                            Metrica
                        </th>
                        @foreach (['current_month', 'previous_month', 'same_month_last_year'] as $period)
                            <th class="py-3 px-4 text-right text-xs font-semibold uppercase tracking-wider {{ $period === 'current_month' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ $periodLabels[$period] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @php
                        $metrics = [
                            'revenue'   => ['label' => 'Fatturato',      'money' => true],
                            'orders'    => ['label' => 'N. Ordini',       'money' => false],
                            'avg_order' => ['label' => 'Ordine Medio',    'money' => true],
                            'returns'   => ['label' => 'Resi',            'money' => false],
                        ];
                        $format = fn($v, $money) => ($money ? '€ ' : '') . number_format($v, $money ? 2 : 0, ',', '.');
                    @endphp
                    @foreach ($metrics as $key => $meta)
                        @php
                            $curr = $data['current_month'][$key];
                            $prev = $data['previous_month'][$key];
                            $change = $prev > 0 ? (($curr - $prev) / $prev) * 100 : null;
                            $isPositiveGood = $key !== 'returns';
                            $isGood = $change !== null && ($isPositiveGood ? $change >= 0 : $change <= 0);
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                            <td class="py-3 px-4 font-medium text-gray-700 dark:text-gray-300">
                                {{ $meta['label'] }}
                            </td>
                            {{-- Current month --}}
                            <td class="py-3 px-4 text-right">
                                <span class="font-semibold text-gray-900 dark:text-white font-mono">
                                    {{ $format($curr, $meta['money']) }}
                                </span>
                                @if ($change !== null)
                                    <span class="ml-2 inline-flex items-center gap-0.5 text-xs {{ $isGood ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                                        @if ($change > 0)
                                            <x-heroicon-m-arrow-trending-up class="h-3 w-3" />
                                        @elseif ($change < 0)
                                            <x-heroicon-m-arrow-trending-down class="h-3 w-3" />
                                        @endif
                                        {{ $change > 0 ? '+' : '' }}{{ number_format($change, 1) }}%
                                    </span>
                                @endif
                            </td>
                            {{-- Previous month --}}
                            <td class="py-3 px-4 text-right font-mono text-gray-500 dark:text-gray-400">
                                {{ $format($prev, $meta['money']) }}
                            </td>
                            {{-- Same month last year --}}
                            <td class="py-3 px-4 text-right font-mono text-gray-500 dark:text-gray-400">
                                @php
                                    $lastYear = $data['same_month_last_year'][$key];
                                    $yoyChange = $lastYear > 0 ? (($curr - $lastYear) / $lastYear) * 100 : null;
                                @endphp
                                {{ $format($lastYear, $meta['money']) }}
                                @if ($yoyChange !== null)
                                    <span class="ml-1 text-xs {{ ($isPositiveGood ? $yoyChange >= 0 : $yoyChange <= 0) ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                                        ({{ $yoyChange > 0 ? '+' : '' }}{{ number_format($yoyChange, 1) }}% YoY)
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
