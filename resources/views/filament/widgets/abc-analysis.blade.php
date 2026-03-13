<x-filament-widgets::widget>
    <x-filament::section
        icon="heroicon-o-funnel"
        icon-color="warning"
        heading="Analisi ABC Prodotti — Principio di Pareto (80/20)"
    >
        @if ($totalRevenue > 0)
            {{-- Summary bar --}}
            <div class="flex items-center gap-4 mb-5 text-xs text-gray-500 dark:text-gray-400">
                <span>Fatturato totale analizzato: <strong class="text-gray-800 dark:text-gray-200">€ {{ number_format($totalRevenue, 2, ',', '.') }}</strong></span>
                @foreach (['A' => ['text-success-600 dark:text-success-400', 'success'], 'B' => ['text-warning-600 dark:text-warning-400', 'warning'], 'C' => ['text-danger-600 dark:text-danger-400', 'danger']] as $cls => $conf)
                    <span class="{{ $conf[0] }} font-medium">Classe {{ $cls }}: {{ count($abcData[$cls]) }} prodotti</span>
                @endforeach
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                @foreach ([
                    'A' => [
                        'label' => 'Classe A',
                        'sub'   => '80% del fatturato — Priorità massima',
                        'header_bg' => 'bg-success-50 dark:bg-success-400/10',
                        'border'    => 'border-success-200 dark:border-success-400/20',
                        'text'      => 'text-success-700 dark:text-success-400',
                        'badge'     => 'bg-success-100 text-success-700 dark:bg-success-400/15 dark:text-success-400',
                        'dot'       => 'bg-success-500',
                    ],
                    'B' => [
                        'label' => 'Classe B',
                        'sub'   => '15% del fatturato — Importanza media',
                        'header_bg' => 'bg-warning-50 dark:bg-warning-400/10',
                        'border'    => 'border-warning-200 dark:border-warning-400/20',
                        'text'      => 'text-warning-700 dark:text-warning-400',
                        'badge'     => 'bg-warning-100 text-warning-700 dark:bg-warning-400/15 dark:text-warning-400',
                        'dot'       => 'bg-warning-500',
                    ],
                    'C' => [
                        'label' => 'Classe C',
                        'sub'   => '5% del fatturato — Bassa priorità',
                        'header_bg' => 'bg-danger-50 dark:bg-danger-400/10',
                        'border'    => 'border-danger-200 dark:border-danger-400/20',
                        'text'      => 'text-danger-700 dark:text-danger-400',
                        'badge'     => 'bg-danger-100 text-danger-700 dark:bg-danger-400/15 dark:text-danger-400',
                        'dot'       => 'bg-danger-500',
                    ],
                ] as $class => $info)
                    <div class="rounded-xl border {{ $info['border'] }} overflow-hidden flex flex-col">
                        {{-- Header --}}
                        <div class="{{ $info['header_bg'] }} px-4 py-3 border-b {{ $info['border'] }} flex items-center justify-between gap-2 shrink-0">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $info['dot'] }}"></span>
                                    <span class="text-sm font-bold {{ $info['text'] }}">{{ $info['label'] }}</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 ml-4">{{ $info['sub'] }}</p>
                            </div>
                            <span class="text-3xl font-black {{ $info['text'] }}">{{ count($abcData[$class]) }}</span>
                        </div>

                        {{-- Product list --}}
                        <div class="divide-y divide-gray-100 dark:divide-gray-700/40 overflow-y-auto max-h-72">
                            @forelse (array_slice($abcData[$class], 0, 20) as $item)
                                <div class="px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-xs font-medium text-gray-800 dark:text-gray-200 truncate flex-1 leading-tight">
                                            {{ $item['product'] }}
                                        </span>
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $info['badge'] }} shrink-0">
                                            € {{ number_format($item['revenue'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[11px] text-gray-400">{{ $item['sku'] ?: '–' }}</span>
                                        <span class="text-[11px] text-gray-300 dark:text-gray-600">|</span>
                                        <span class="text-[11px] text-gray-400">{{ $item['quantity'] }} unità</span>
                                        <span class="text-[11px] text-gray-300 dark:text-gray-600">|</span>
                                        <span class="text-[11px] {{ $info['text'] }} font-medium">{{ $item['percentage'] }}%</span>
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-6 text-center text-xs text-gray-400">
                                    Nessun prodotto in questa classe
                                </div>
                            @endforelse

                            @if (count($abcData[$class]) > 20)
                                <div class="px-4 py-2.5 text-center text-xs text-gray-400 bg-gray-50 dark:bg-white/[0.02]">
                                    +{{ count($abcData[$class]) - 20 }} altri prodotti
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                <x-heroicon-o-chart-bar class="h-14 w-14 mb-3 opacity-25" />
                <p class="text-sm">Nessun dato di vendita disponibile per l'analisi ABC.</p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
