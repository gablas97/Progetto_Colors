<x-filament-panels::page>
    {{-- PREVISIONI VENDITE --}}
    <x-filament::section heading="Previsioni Vendite" icon="heroicon-o-chart-bar" icon-color="primary">
        <div class="grid grid-cols-9 gap-2">
            @foreach ($salesForecast as $item)
                <div class="text-center">
                    <div class="text-xs text-gray-500 mb-1">{{ $item['month'] }}</div>
                    @php
                        $value = $item['actual'] ?? $item['forecast'] ?? 0;
                        $max = max(array_map(fn($i) => max($i['actual'] ?? 0, $i['forecast'] ?? 0), $salesForecast));
                        $height = $max > 0 ? ($value / $max * 120) : 0;
                    @endphp
                    <div class="flex justify-center items-end h-32">
                        <div class="w-8 rounded-t {{ $item['type'] === 'forecast' ? 'bg-primary-300 dark:bg-primary-700 border-2 border-dashed border-primary-500' : 'bg-primary-500' }}"
                             style="height: {{ $height }}px"></div>
                    </div>
                    <div class="text-xs font-medium mt-1">€ {{ number_format($value, 0, ',', '.') }}</div>
                    @if($item['type'] === 'forecast')
                        <span class="text-[10px] text-primary-500">Previsione</span>
                    @endif
                </div>
            @endforeach
        </div>
    </x-filament::section>

    {{-- ANALISI ABC --}}
    <x-filament::section heading="Analisi ABC Prodotti (Pareto)" icon="heroicon-o-funnel" icon-color="warning">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            @foreach (['A' => ['label' => 'Classe A (80% fatturato)', 'color' => 'success', 'desc' => 'Prodotti con il maggior contributo al fatturato'], 'B' => ['label' => 'Classe B (15% fatturato)', 'color' => 'warning', 'desc' => 'Prodotti con contributo medio'], 'C' => ['label' => 'Classe C (5% fatturato)', 'color' => 'danger', 'desc' => 'Prodotti con contributo marginale']] as $class => $info)
                <div>
                    <h4 class="text-sm font-semibold text-{{ $info['color'] }}-600 mb-1">{{ $info['label'] }}</h4>
                    <p class="text-xs text-gray-400 mb-2">{{ $info['desc'] }}</p>
                    <div class="text-xs text-gray-500 mb-1">{{ count($abcAnalysis[$class]) }} prodotti</div>
                    @if(count($abcAnalysis[$class]) > 0)
                        <div class="divide-y dark:divide-gray-700 max-h-60 overflow-y-auto">
                            @foreach (array_slice($abcAnalysis[$class], 0, 10) as $item)
                                <div class="py-1.5">
                                    <div class="flex justify-between">
                                        <span class="text-xs font-medium truncate flex-1">{{ $item['product'] }}</span>
                                        <span class="text-xs ml-2">€ {{ number_format($item['revenue'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="text-[10px] text-gray-400">{{ $item['sku'] }} · {{ $item['percentage'] }}% del fatturato</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-400">Nessun dato</p>
                    @endif
                </div>
            @endforeach
        </div>
    </x-filament::section>

    {{-- CONFRONTO PERFORMANCE --}}
    <x-filament::section heading="Confronto Performance" icon="heroicon-o-scale" icon-color="info">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 dark:text-gray-400">
                        <th class="py-2">Metrica</th>
                        <th class="py-2 text-right">Mese Corrente</th>
                        <th class="py-2 text-right">Mese Precedente</th>
                        <th class="py-2 text-right">Stesso Mese Anno Prec.</th>
                    </tr>
                </thead>
                <tbody class="divide-y dark:divide-gray-700">
                    @php $metrics = ['revenue' => 'Fatturato', 'orders' => 'Ordini', 'avg_order' => 'Ordine Medio', 'returns' => 'Resi']; @endphp
                    @foreach ($metrics as $key => $label)
                        <tr>
                            <td class="py-2 font-medium">{{ $label }}</td>
                            <td class="py-2 text-right font-mono">
                                @if(in_array($key, ['revenue', 'avg_order']))
                                    € {{ number_format($performanceComparison['current_month'][$key], 2, ',', '.') }}
                                @else
                                    {{ $performanceComparison['current_month'][$key] }}
                                @endif
                            </td>
                            <td class="py-2 text-right font-mono">
                                @if(in_array($key, ['revenue', 'avg_order']))
                                    € {{ number_format($performanceComparison['previous_month'][$key], 2, ',', '.') }}
                                @else
                                    {{ $performanceComparison['previous_month'][$key] }}
                                @endif
                                @php
                                    $prev = $performanceComparison['previous_month'][$key];
                                    $curr = $performanceComparison['current_month'][$key];
                                    $diff = $prev > 0 ? (($curr - $prev) / $prev * 100) : 0;
                                @endphp
                                @if($diff != 0)
                                    <span class="text-xs {{ $diff > 0 ? 'text-success-600' : 'text-danger-600' }}">
                                        {{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 1) }}%
                                    </span>
                                @endif
                            </td>
                            <td class="py-2 text-right font-mono">
                                @if(in_array($key, ['revenue', 'avg_order']))
                                    € {{ number_format($performanceComparison['same_month_last_year'][$key], 2, ',', '.') }}
                                @else
                                    {{ $performanceComparison['same_month_last_year'][$key] }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>

    {{-- SUGGERIMENTI RIORDINO --}}
    <x-filament::section heading="Suggerimenti Riordino Intelligente" icon="heroicon-o-light-bulb" icon-color="success">
        @if(count($reorderSuggestions) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 dark:text-gray-400">
                            <th class="py-2">Urgenza</th>
                            <th class="py-2">Prodotto</th>
                            <th class="py-2">Marca</th>
                            <th class="py-2 text-right">Stock</th>
                            <th class="py-2 text-right">Soglia</th>
                            <th class="py-2 text-right">Vendite/Giorno</th>
                            <th class="py-2 text-right">Giorni Rimanenti</th>
                            <th class="py-2 text-right">Qtà Suggerita</th>
                            <th class="py-2 text-right">Costo Stimato</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        @foreach ($reorderSuggestions as $item)
                            <tr>
                                <td class="py-2">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                        {{ match($item['urgency']) { 'critica' => 'bg-danger-100 text-danger-700 dark:bg-danger-400/10 dark:text-danger-400', 'alta' => 'bg-warning-100 text-warning-700 dark:bg-warning-400/10 dark:text-warning-400', 'media' => 'bg-info-100 text-info-700 dark:bg-info-400/10 dark:text-info-400', default => 'bg-gray-100 text-gray-700 dark:bg-gray-400/10 dark:text-gray-400' } }}">
                                        {{ ucfirst($item['urgency']) }}
                                    </span>
                                </td>
                                <td class="py-2">
                                    <div class="font-medium">{{ $item['product'] }}</div>
                                    <div class="text-xs text-gray-400">{{ $item['sku'] }}</div>
                                </td>
                                <td class="py-2 text-gray-500">{{ $item['brand'] }}</td>
                                <td class="py-2 text-right font-mono">{{ $item['current_stock'] }}</td>
                                <td class="py-2 text-right font-mono">{{ $item['threshold'] }}</td>
                                <td class="py-2 text-right font-mono">{{ $item['daily_sales_rate'] }}</td>
                                <td class="py-2 text-right font-mono {{ $item['days_of_stock'] <= 7 ? 'text-danger-600 font-bold' : '' }}">
                                    {{ $item['days_of_stock'] >= 999 ? '∞' : $item['days_of_stock'] }}
                                </td>
                                <td class="py-2 text-right font-mono font-bold text-primary-600">{{ $item['suggested_qty'] }}</td>
                                <td class="py-2 text-right font-mono">€ {{ number_format($item['estimated_cost'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-500">Nessun prodotto necessita di riordino al momento.</p>
        @endif
    </x-filament::section>

    {{-- TREND VENDITE PER CATEGORIA --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <x-filament::section heading="Trend Vendite per Categoria" icon="heroicon-o-tag" icon-color="primary">
            @if(count($salesTrendByCategory) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="py-1">Mese</th>
                                @foreach (array_keys(array_diff_key($salesTrendByCategory[0] ?? [], ['month' => true])) as $cat)
                                    <th class="py-1 text-right">{{ $cat }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @foreach ($salesTrendByCategory as $row)
                                <tr>
                                    <td class="py-1">{{ $row['month'] }}</td>
                                    @foreach (array_diff_key($row, ['month' => true]) as $val)
                                        <td class="py-1 text-right font-mono">€ {{ number_format($val, 0, ',', '.') }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500">Dati insufficienti.</p>
            @endif
        </x-filament::section>

        <x-filament::section heading="Trend Vendite per Marca" icon="heroicon-o-building-storefront" icon-color="warning">
            @if(count($salesTrendByBrand) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="py-1">Mese</th>
                                @foreach (array_keys(array_diff_key($salesTrendByBrand[0] ?? [], ['month' => true])) as $brand)
                                    <th class="py-1 text-right">{{ $brand }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @foreach ($salesTrendByBrand as $row)
                                <tr>
                                    <td class="py-1">{{ $row['month'] }}</td>
                                    @foreach (array_diff_key($row, ['month' => true]) as $val)
                                        <td class="py-1 text-right font-mono">€ {{ number_format($val, 0, ',', '.') }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500">Dati insufficienti.</p>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
