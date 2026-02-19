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

        {{-- KPI --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <x-filament::section>
                <div class="text-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Resi Totali</div>
                    <div class="text-2xl font-bold text-danger-600">{{ number_format($totalReturns) }}</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Valore Totale Resi</div>
                    <div class="text-2xl font-bold text-warning-600">€ {{ number_format($totalReturnValue, 2, ',', '.') }}</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Tasso di Reso</div>
                    <div class="text-2xl font-bold text-primary-600">{{ $returnRate }}%</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Valore Medio Reso</div>
                    <div class="text-2xl font-bold">€ {{ $totalReturns > 0 ? number_format($totalReturnValue / $totalReturns, 2, ',', '.') : '0,00' }}</div>
                </div>
            </x-filament::section>
        </div>

        {{-- RESI PER STATO + MOVIMENTI MAGAZZINO --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-filament::section heading="Resi per Stato" icon="heroicon-o-arrow-uturn-left">
                @if($returnsByStatus->count() > 0)
                    <div class="divide-y dark:divide-gray-700">
                        @foreach ($returnsByStatus as $row)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm font-medium capitalize">{{ str_replace('_', ' ', $row->return_status) }}</span>
                                <div class="text-right">
                                    <span class="text-sm font-bold">{{ $row->count }}</span>
                                    <span class="text-xs text-gray-400 ml-2">€ {{ number_format($row->total ?? 0, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Nessun reso nel periodo.</p>
                @endif
            </x-filament::section>

            <x-filament::section heading="Movimenti Magazzino Resi" icon="heroicon-o-archive-box">
                @if($warehouseReturns->count() > 0)
                    <div class="divide-y dark:divide-gray-700">
                        @foreach ($warehouseReturns as $row)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm font-medium capitalize">{{ str_replace('_', ' ', $row->reason) }}</span>
                                <div class="text-right">
                                    <span class="text-sm font-bold">{{ $row->count }} movimenti</span>
                                    <span class="text-xs text-gray-400 ml-2">{{ number_format($row->total_qty) }} pezzi</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Nessun movimento reso nel periodo.</p>
                @endif
            </x-filament::section>
        </div>

        {{-- ULTIMI RESI --}}
        <x-filament::section heading="Ultimi Resi" icon="heroicon-o-clock">
            @if($recentReturns->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="text-left py-2 px-3">Ordine</th>
                                <th class="text-left py-2 px-3">Cliente</th>
                                <th class="text-left py-2 px-3">Stato</th>
                                <th class="text-left py-2 px-3">Motivo</th>
                                <th class="text-right py-2 px-3">Totale</th>
                                <th class="text-right py-2 px-3">Data Richiesta</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @foreach ($recentReturns as $order)
                                <tr>
                                    <td class="py-2 px-3 font-medium">{{ $order->order_number }}</td>
                                    <td class="py-2 px-3">{{ $order->user?->name ?? 'N/D' }}</td>
                                    <td class="py-2 px-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                            @if($order->return_status === 'approved') bg-success-100 text-success-800
                                            @elseif($order->return_status === 'requested') bg-warning-100 text-warning-800
                                            @elseif($order->return_status === 'rejected') bg-danger-100 text-danger-800
                                            @elseif($order->return_status === 'refunded') bg-primary-100 text-primary-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $order->return_status)) }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3 text-gray-500">{{ $order->return_reason ?? '-' }}</td>
                                    <td class="py-2 px-3 text-right font-medium">€ {{ number_format($order->total, 2, ',', '.') }}</td>
                                    <td class="py-2 px-3 text-right text-gray-500">{{ $order->return_requested_at?->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500">Nessun reso recente.</p>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
