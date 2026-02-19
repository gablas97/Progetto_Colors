<x-filament-panels::page>
    <div class="space-y-6">
        {{-- FILTRI DATA --}}
        <x-filament::section>
            <div class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Da</label>
                    <input type="date" wire:model.live="dateFrom" class="mt-1 block rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">A</label>
                    <input type="date" wire:model.live="dateTo" class="mt-1 block rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <x-filament::button wire:click="downloadPdf" icon="heroicon-o-arrow-down-tray" color="success">
                    Scarica PDF
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- KPI PRINCIPALI --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
            <x-filament::section>
                <div class="text-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Fatturato Totale</div>
                    <div class="text-2xl font-bold text-primary-600">€ {{ number_format($totalRevenue, 2, ',', '.') }}</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Ordini Totali</div>
                    <div class="text-2xl font-bold">{{ number_format($totalOrders) }}</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Valore Medio Ordine</div>
                    <div class="text-2xl font-bold text-success-600">€ {{ number_format($avgOrderValue, 2, ',', '.') }}</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">IVA Totale</div>
                    <div class="text-2xl font-bold text-warning-600">€ {{ number_format($totalTax, 2, ',', '.') }}</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Sconti Applicati</div>
                    <div class="text-2xl font-bold text-danger-600">€ {{ number_format($totalDiscount, 2, ',', '.') }}</div>
                </div>
            </x-filament::section>
        </div>

        {{-- DETTAGLI --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- PER STATO --}}
            <x-filament::section heading="Ordini per Stato" icon="heroicon-o-clipboard-document-list">
                @if($ordersByStatus->count() > 0)
                    <div class="divide-y dark:divide-gray-700">
                        @foreach ($ordersByStatus as $row)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm font-medium capitalize">{{ $row->status }}</span>
                                <div class="text-right">
                                    <span class="text-sm font-bold">{{ $row->count }}</span>
                                    <span class="text-xs text-gray-400 ml-2">€ {{ number_format($row->total ?? 0, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Nessun dato nel periodo.</p>
                @endif
            </x-filament::section>

            {{-- PER PAGAMENTO --}}
            <x-filament::section heading="Per Metodo Pagamento" icon="heroicon-o-credit-card">
                @if($ordersByPayment->count() > 0)
                    <div class="divide-y dark:divide-gray-700">
                        @foreach ($ordersByPayment as $row)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm font-medium capitalize">{{ $row->payment_method ?? 'N/D' }}</span>
                                <div class="text-right">
                                    <span class="text-sm font-bold">{{ $row->count }}</span>
                                    <span class="text-xs text-gray-400 ml-2">€ {{ number_format($row->total ?? 0, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Nessun dato nel periodo.</p>
                @endif
            </x-filament::section>

            {{-- PER FONTE --}}
            <x-filament::section heading="Per Fonte Ordine" icon="heroicon-o-globe-alt">
                @if($ordersBySource->count() > 0)
                    <div class="divide-y dark:divide-gray-700">
                        @foreach ($ordersBySource as $row)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm font-medium capitalize">{{ $row->source ?? 'N/D' }}</span>
                                <div class="text-right">
                                    <span class="text-sm font-bold">{{ $row->count }}</span>
                                    <span class="text-xs text-gray-400 ml-2">€ {{ number_format($row->total ?? 0, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Nessun dato nel periodo.</p>
                @endif
            </x-filament::section>
        </div>

        {{-- FATTURE --}}
        <x-filament::section heading="Riepilogo Fatture" icon="heroicon-o-document-text">
            @if($invoicesSummary->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @foreach ($invoicesSummary as $row)
                        <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <div class="text-sm font-medium capitalize">{{ $row->status }}</div>
                            <div class="text-lg font-bold">{{ $row->count }}</div>
                            <div class="text-xs text-gray-400">€ {{ number_format($row->total ?? 0, 2, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">Nessuna fattura nel periodo.</p>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
