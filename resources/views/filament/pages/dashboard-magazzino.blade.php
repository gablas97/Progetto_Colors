<x-filament-panels::page>
    {{-- KPI PRINCIPALI --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        <x-filament::section>
            <div class="text-center">
                <div class="text-sm text-gray-500 dark:text-gray-400">Prodotti a Magazzino</div>
                <div class="text-2xl font-bold">{{ $totalProducts }}</div>
                <div class="text-xs text-gray-400">{{ number_format($totalItems) }} pezzi totali</div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <div class="text-sm text-gray-500 dark:text-gray-400">Valore al Costo</div>
                <div class="text-2xl font-bold text-primary-600">€ {{ number_format($warehouseValue, 2, ',', '.') }}</div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <div class="text-sm text-gray-500 dark:text-gray-400">Valore al Dettaglio</div>
                <div class="text-2xl font-bold text-success-600">€ {{ number_format($warehouseRetailValue, 2, ',', '.') }}</div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <div class="text-sm text-gray-500 dark:text-gray-400">Stato Stock</div>
                <div class="flex justify-center gap-3 mt-1">
                    <span class="text-xs"><span class="font-bold text-success-600">{{ $healthyStockCount }}</span> OK</span>
                    <span class="text-xs"><span class="font-bold text-warning-600">{{ $lowStockCount }}</span> Basso</span>
                    <span class="text-xs"><span class="font-bold text-danger-600">{{ $outOfStockCount }}</span> Esauriti</span>
                </div>
            </div>
        </x-filament::section>
    </div>

    {{-- MOVIMENTI 30 GIORNI + GRAFICO --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <x-filament::section heading="Movimenti Ultimi 30 Giorni">
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center p-3 bg-success-50 dark:bg-success-900/20 rounded-lg">
                    <div class="text-lg font-bold text-success-600">{{ $movementsLast30->get('carico')?->total_qty ?? 0 }}</div>
                    <div class="text-xs text-gray-500">Carichi ({{ $movementsLast30->get('carico')?->count ?? 0 }} op.)</div>
                </div>
                <div class="text-center p-3 bg-danger-50 dark:bg-danger-900/20 rounded-lg">
                    <div class="text-lg font-bold text-danger-600">{{ $movementsLast30->get('scarico')?->total_qty ?? 0 }}</div>
                    <div class="text-xs text-gray-500">Scarichi ({{ $movementsLast30->get('scarico')?->count ?? 0 }} op.)</div>
                </div>
                <div class="text-center p-3 bg-warning-50 dark:bg-warning-900/20 rounded-lg">
                    <div class="text-lg font-bold text-warning-600">{{ $movementsLast30->get('reso')?->total_qty ?? 0 }}</div>
                    <div class="text-xs text-gray-500">Resi ({{ $movementsLast30->get('reso')?->count ?? 0 }} op.)</div>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section heading="Attività Giornaliera (14 gg)">
            <div class="flex items-end gap-1 h-32">
                @foreach ($dailyMovements as $day)
                    @php
                        $maxDay = max(array_column($dailyMovements, 'carichi')) + max(array_column($dailyMovements, 'scarichi'));
                        $totalDay = $day['carichi'] + $day['scarichi'];
                        $height = $maxDay > 0 ? ($totalDay / $maxDay * 100) : 0;
                        $caricoHeight = $totalDay > 0 ? ($day['carichi'] / max($totalDay, 1) * $height) : 0;
                        $scaricoHeight = $height - $caricoHeight;
                    @endphp
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full flex flex-col items-center justify-end" style="height: 100px">
                            <div class="w-full bg-danger-400 rounded-t" style="height: {{ $scaricoHeight }}px"></div>
                            <div class="w-full bg-success-400 rounded-b" style="height: {{ $caricoHeight }}px"></div>
                        </div>
                        <span class="text-[9px] text-gray-400 mt-1">{{ $day['date'] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-center gap-4 mt-2">
                <span class="flex items-center gap-1 text-xs"><span class="w-3 h-3 bg-success-400 rounded"></span> Carichi</span>
                <span class="flex items-center gap-1 text-xs"><span class="w-3 h-3 bg-danger-400 rounded"></span> Scarichi</span>
            </div>
        </x-filament::section>
    </div>

    {{-- TOP ROTAZIONE E STOCK PER CATEGORIA --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <x-filament::section heading="Top 10 Indice di Rotazione" icon="heroicon-o-arrow-path">
            <div class="divide-y dark:divide-gray-700">
                @foreach ($topRotation as $item)
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <div class="text-sm font-medium">{{ $item['name'] }}</div>
                            <div class="text-xs text-gray-400">{{ $item['sku'] }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-primary-600">{{ $item['rotation'] }}x</div>
                            <div class="text-xs text-gray-400">{{ $item['stock'] }} in stock / {{ $item['sales'] }} venduti</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <x-filament::section heading="Stock per Categoria" icon="heroicon-o-tag">
            @if($stockByCategory->count() > 0)
                <div class="divide-y dark:divide-gray-700">
                    @foreach ($stockByCategory as $cat)
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm font-medium">{{ $cat->name }}</span>
                            <div class="text-right">
                                <div class="text-sm">{{ number_format($cat->total_qty) }} pz</div>
                                <div class="text-xs text-gray-400">€ {{ number_format($cat->total_value, 2, ',', '.') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">Nessun dato disponibile.</p>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
