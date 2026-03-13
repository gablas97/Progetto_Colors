<x-filament-widgets::widget>
    <x-filament::section
        icon="heroicon-o-light-bulb"
        icon-color="success"
        heading="Suggerimenti Riordino Intelligente"
    >
        @if (count($suggestions) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <th class="py-2.5 px-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Urgenza</th>
                            <th class="py-2.5 px-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Prodotto</th>
                            <th class="py-2.5 px-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Marca</th>
                            <th class="py-2.5 px-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Stock</th>
                            <th class="py-2.5 px-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Soglia</th>
                            <th class="py-2.5 px-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Vend./gg</th>
                            <th class="py-2.5 px-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Giorni rimasti</th>
                            <th class="py-2.5 px-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Qtà suggerita</th>
                            <th class="py-2.5 px-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Costo stimato</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @foreach ($suggestions as $item)
                            @php
                                $urgencyMap = [
                                    'critica' => ['class' => 'bg-danger-100 text-danger-700 dark:bg-danger-400/15 dark:text-danger-400',   'label' => 'Critica'],
                                    'alta'    => ['class' => 'bg-warning-100 text-warning-700 dark:bg-warning-400/15 dark:text-warning-400', 'label' => 'Alta'],
                                    'media'   => ['class' => 'bg-info-100 text-info-700 dark:bg-info-400/15 dark:text-info-400',             'label' => 'Media'],
                                    'bassa'   => ['class' => 'bg-gray-100 text-gray-600 dark:bg-gray-400/10 dark:text-gray-400',             'label' => 'Bassa'],
                                ];
                                $uc = $urgencyMap[$item['urgency']] ?? $urgencyMap['bassa'];
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                <td class="py-2.5 px-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $uc['class'] }}">
                                        {{ $uc['label'] }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3">
                                    <div class="font-medium text-gray-800 dark:text-gray-200 leading-tight">{{ $item['product'] }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $item['sku'] }}</div>
                                </td>
                                <td class="py-2.5 px-3 text-gray-500 dark:text-gray-400 text-xs">{{ $item['brand'] }}</td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold {{ $item['current_stock'] <= 0 ? 'text-danger-600 dark:text-danger-400' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ $item['current_stock'] }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono text-gray-500 dark:text-gray-400">{{ $item['threshold'] }}</td>
                                <td class="py-2.5 px-3 text-right font-mono text-gray-600 dark:text-gray-400">{{ $item['daily_sales_rate'] }}</td>
                                <td class="py-2.5 px-3 text-right font-mono {{ $item['days_of_stock'] < 999 && $item['days_of_stock'] <= 7 ? 'text-danger-600 dark:text-danger-400 font-bold' : 'text-gray-600 dark:text-gray-400' }}">
                                    {{ $item['days_of_stock'] >= 999 ? '∞' : $item['days_of_stock'] }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-primary-600 dark:text-primary-400">
                                    {{ $item['suggested_qty'] }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono text-gray-700 dark:text-gray-300">
                                    € {{ number_format($item['estimated_cost'], 2, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                <x-heroicon-o-check-badge class="h-12 w-12 mb-3 opacity-30" />
                <p class="text-sm font-medium">Ottimo! Nessun prodotto necessita di riordino al momento.</p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
