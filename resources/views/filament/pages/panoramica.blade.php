<x-filament-panels::page>
    {{-- STATISTICHE GENERALI --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
        <x-filament::section>
            <div class="text-center">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Valore Magazzino</div>
                <div class="text-2xl font-bold text-primary-600">€ {{ number_format($warehouseValue, 2, ',', '.') }}</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Fatturato Mese</div>
                <div class="text-2xl font-bold text-success-600">€ {{ number_format($monthlyRevenue, 2, ',', '.') }}</div>
                <div class="text-xs text-gray-400">{{ $monthlyOrderCount }} ordini</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Ordini Mensili</div>
                <div class="text-2xl font-bold">{{ $monthlyOrderCount }}</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Alert Stock</div>
                <div class="text-2xl font-bold text-warning-600">{{ $lowStockCount }}</div>
                <div class="text-xs text-gray-400">{{ $outOfStockCount }} esauriti</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Resi Mensili</div>
                <div class="text-2xl font-bold text-danger-600">{{ $monthlyReturns }}</div>
            </div>
        </x-filament::section>
    </div>

    {{-- FILTRO PERIODO + GRAFICI --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mt-6">
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center justify-between">
                    <span>Fatturato</span>
                    <div class="flex gap-1">
                        @foreach (['week' => 'Settimana', 'month' => 'Mese', 'quarter' => 'Trimestre', 'year' => 'Anno'] as $key => $label)
                            <x-filament::button
                                size="xs"
                                :color="$period === $key ? 'primary' : 'gray'"
                                wire:click="$set('period', '{{ $key }}')"
                            >
                                {{ $label }}
                            </x-filament::button>
                        @endforeach
                    </div>
                </div>
            </x-slot>

            @if(count($revenueData) > 0)
                <div class="space-y-2">
                    @foreach ($revenueData as $item)
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-500 w-16">{{ $item['label'] }}</span>
                            <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-full h-4">
                                @php $maxVal = max(array_column($revenueData, 'value')); @endphp
                                <div class="bg-primary-500 h-4 rounded-full" style="width: {{ $maxVal > 0 ? ($item['value'] / $maxVal * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-xs font-medium w-20 text-right">€ {{ number_format($item['value'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">Nessun dato disponibile per il periodo selezionato.</p>
            @endif
        </x-filament::section>

        <x-filament::section heading="Prodotti Più Venduti">
            @if(count($topProductsData) > 0)
                <div class="space-y-2">
                    @foreach ($topProductsData as $i => $item)
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-gray-400 w-6">{{ $i + 1 }}.</span>
                            <span class="text-sm flex-1 truncate">{{ $item['name'] }}</span>
                            <span class="text-xs text-gray-500">{{ $item['sales'] }} venduti</span>
                            <span class="text-xs font-medium">€ {{ number_format($item['revenue'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">Nessun dato di vendita disponibile.</p>
            @endif
        </x-filament::section>
    </div>

    {{-- PRODOTTI IN ESAURIMENTO / ESAURITI --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mt-6">
        <x-filament::section heading="Prodotti in Esaurimento" icon="heroicon-o-exclamation-triangle" icon-color="warning">
            @if($lowStockProducts->count() > 0)
                <div class="divide-y dark:divide-gray-700">
                    @foreach ($lowStockProducts as $product)
                        <div class="flex items-center justify-between py-2">
                            <div>
                                <div class="text-sm font-medium">{{ $product->name }}</div>
                                <div class="text-xs text-gray-400">SKU: {{ $product->sku }} {{ $product->brand ? '| ' . $product->brand->name : '' }}</div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-warning-50 text-warning-700 dark:bg-warning-400/10 dark:text-warning-400">
                                    {{ $product->stock_quantity }} / {{ $product->low_stock_threshold }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">Nessun prodotto in esaurimento.</p>
            @endif
        </x-filament::section>

        <x-filament::section heading="Prodotti Esauriti" icon="heroicon-o-x-circle" icon-color="danger">
            @if($outOfStockProducts->count() > 0)
                <div class="divide-y dark:divide-gray-700">
                    @foreach ($outOfStockProducts as $product)
                        <div class="flex items-center justify-between py-2">
                            <div>
                                <div class="text-sm font-medium">{{ $product->name }}</div>
                                <div class="text-xs text-gray-400">SKU: {{ $product->sku }}</div>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-danger-50 text-danger-700 dark:bg-danger-400/10 dark:text-danger-400">
                                Esaurito
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">Nessun prodotto esaurito.</p>
            @endif
        </x-filament::section>
    </div>

    {{-- SCADENZE E MOVIMENTI --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mt-6">
        <x-filament::section heading="Prossime Scadenze" icon="heroicon-o-calendar" icon-color="primary">
            @if($overdueEvents->count() > 0)
                <div class="mb-3">
                    <div class="text-xs font-semibold text-danger-600 uppercase mb-1">Scadute</div>
                    @foreach ($overdueEvents as $event)
                        <div class="flex items-center gap-2 py-1">
                            <span class="w-2 h-2 rounded-full bg-danger-500"></span>
                            <span class="text-sm flex-1">{{ $event->title }}</span>
                            <span class="text-xs text-danger-500">{{ $event->starts_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($upcomingEvents->count() > 0)
                @foreach ($upcomingEvents as $event)
                    <div class="flex items-center gap-2 py-1">
                        <span class="w-2 h-2 rounded-full" style="background: {{ $event->color }}"></span>
                        <span class="text-sm flex-1">{{ $event->title }}</span>
                        <span class="text-xs text-gray-500">{{ $event->starts_at->format('d/m H:i') }}</span>
                    </div>
                @endforeach
            @else
                <p class="text-sm text-gray-500">Nessuna scadenza imminente.</p>
            @endif
        </x-filament::section>

        <x-filament::section heading="Movimenti Recenti" icon="heroicon-o-arrows-right-left" icon-color="info">
            @if($recentMovements->count() > 0)
                <div class="divide-y dark:divide-gray-700">
                    @foreach ($recentMovements as $mov)
                        <div class="flex items-center justify-between py-2">
                            <div>
                                <div class="text-sm">
                                    <span class="font-medium
                                        @if($mov->type === 'carico') text-success-600
                                        @elseif($mov->type === 'scarico') text-danger-600
                                        @else text-warning-600
                                        @endif
                                    ">{{ $mov->getTypeLabel() }}</span>
                                    - {{ $mov->product?->name ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-400">{{ $mov->getReasonLabel() }} · {{ $mov->user?->full_name ?? 'Sistema' }}</div>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-mono {{ $mov->quantity > 0 ? 'text-success-600' : 'text-danger-600' }}">
                                    {{ $mov->quantity > 0 ? '+' : '' }}{{ $mov->quantity }}
                                </span>
                                <div class="text-xs text-gray-400">{{ $mov->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">Nessun movimento recente.</p>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
