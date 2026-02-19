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
                    <div class="text-sm text-gray-500 dark:text-gray-400">Carte Attive</div>
                    <div class="text-2xl font-bold text-primary-600">{{ number_format($totalCards) }}</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Punti Totali in Circolo</div>
                    <div class="text-2xl font-bold text-warning-600">{{ number_format($totalPoints) }}</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Spesa Totale Clienti</div>
                    <div class="text-2xl font-bold text-success-600">€ {{ number_format($totalSpent, 2, ',', '.') }}</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Media Spesa/Carta</div>
                    <div class="text-2xl font-bold">€ {{ $totalCards > 0 ? number_format($totalSpent / $totalCards, 2, ',', '.') : '0,00' }}</div>
                </div>
            </x-filament::section>
        </div>

        {{-- CARTE PER TIER + TRANSAZIONI --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-filament::section heading="Distribuzione per Tier" icon="heroicon-o-star">
                @if($cardsByTier->count() > 0)
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($cardsByTier as $tier)
                            <div class="text-center p-3 rounded-lg
                                @if($tier->tier === 'platinum') bg-purple-50 dark:bg-purple-900/20
                                @elseif($tier->tier === 'gold') bg-yellow-50 dark:bg-yellow-900/20
                                @elseif($tier->tier === 'silver') bg-gray-100 dark:bg-gray-800
                                @else bg-blue-50 dark:bg-blue-900/20
                                @endif">
                                <div class="text-sm font-medium capitalize">{{ $tier->tier }}</div>
                                <div class="text-2xl font-bold
                                    @if($tier->tier === 'platinum') text-purple-600
                                    @elseif($tier->tier === 'gold') text-yellow-600
                                    @elseif($tier->tier === 'silver') text-gray-600
                                    @else text-blue-600
                                    @endif">
                                    {{ $tier->count }}
                                </div>
                                <div class="text-xs text-gray-400">{{ $totalCards > 0 ? round($tier->count / $totalCards * 100, 1) : 0 }}%</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Nessuna carta attiva.</p>
                @endif
            </x-filament::section>

            <x-filament::section heading="Transazioni nel Periodo" icon="heroicon-o-arrows-right-left">
                @if($transactionsSummary->count() > 0)
                    <div class="divide-y dark:divide-gray-700">
                        @foreach ($transactionsSummary as $row)
                            <div class="flex items-center justify-between py-3">
                                <div>
                                    <span class="text-sm font-medium capitalize">{{ str_replace('_', ' ', $row->type) }}</span>
                                    <span class="text-xs text-gray-400 ml-2">({{ $row->count }} operazioni)</span>
                                </div>
                                <span class="text-lg font-bold
                                    @if($row->type === 'earn') text-success-600
                                    @elseif($row->type === 'redeem') text-danger-600
                                    @else text-gray-600
                                    @endif">
                                    {{ number_format($row->total_points) }} punti
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Nessuna transazione nel periodo.</p>
                @endif
            </x-filament::section>
        </div>

        {{-- TOP CLIENTI --}}
        <x-filament::section heading="Top 10 Clienti per Spesa" icon="heroicon-o-trophy">
            @if($topCustomers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="text-left py-2 px-3">#</th>
                                <th class="text-left py-2 px-3">Cliente</th>
                                <th class="text-left py-2 px-3">Carta</th>
                                <th class="text-center py-2 px-3">Tier</th>
                                <th class="text-right py-2 px-3">Punti</th>
                                <th class="text-right py-2 px-3">Spesa Totale</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @foreach ($topCustomers as $index => $card)
                                <tr>
                                    <td class="py-2 px-3 font-bold text-gray-400">{{ $index + 1 }}</td>
                                    <td class="py-2 px-3 font-medium">{{ $card->user?->name ?? 'N/D' }}</td>
                                    <td class="py-2 px-3 text-gray-500 font-mono text-xs">{{ $card->card_number }}</td>
                                    <td class="py-2 px-3 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                            @if($card->tier === 'platinum') bg-purple-100 text-purple-800
                                            @elseif($card->tier === 'gold') bg-yellow-100 text-yellow-800
                                            @elseif($card->tier === 'silver') bg-gray-100 text-gray-800
                                            @else bg-blue-100 text-blue-800
                                            @endif">
                                            {{ ucfirst($card->tier) }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3 text-right">{{ number_format($card->points) }}</td>
                                    <td class="py-2 px-3 text-right font-bold text-success-600">€ {{ number_format($card->total_spent, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500">Nessun cliente nel programma fedeltà.</p>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
