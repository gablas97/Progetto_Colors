<x-filament-panels::page>
    <div class="space-y-6">

        {{-- ===== TAB NAVIGATION ===== --}}
        <div class="flex gap-0 border-b border-gray-200 dark:border-gray-700 -mt-2">
            <button
                wire:click="switchTab('panoramica')"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium transition-colors focus:outline-none {{ $tab === 'panoramica' ? 'border-b-2 border-primary-500 text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}"
            >
                <x-heroicon-o-home class="h-4 w-4" />
                Panoramica
            </button>
            <button
                wire:click="switchTab('analytics')"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium transition-colors focus:outline-none {{ $tab === 'analytics' ? 'border-b-2 border-primary-500 text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}"
            >
                <x-heroicon-o-chart-bar-square class="h-4 w-4" />
                Analytics Avanzate
            </button>
        </div>

        {{-- ===== PANORAMICA TAB ===== --}}
        @if ($tab === 'panoramica')
            <div class="space-y-6">

                {{-- KPI Stats --}}
                @livewire(\App\Filament\Widgets\StatsOverview::class)

                {{-- Andamento fatturato --}}
                @livewire(\App\Filament\Widgets\RevenueChart::class)

                {{-- Top prodotti + Recensioni recenti --}}
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    @livewire(\App\Filament\Widgets\TopProductsChart::class)
                    @livewire(\App\Filament\Widgets\RecentReviews::class)
                </div>

                {{-- Ultimi ordini --}}
                @livewire(\App\Filament\Widgets\LatestOrders::class)

                {{-- Prodotti più venduti (tabella dettagliata) --}}
                @livewire(\App\Filament\Widgets\PopularProducts::class)

                {{-- Stock basso + Scadenze --}}
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    @livewire(\App\Filament\Widgets\LowStockAlert::class)
                    @livewire(\App\Filament\Widgets\UpcomingEventsWidget::class)
                </div>

                {{-- Movimenti recenti --}}
                @livewire(\App\Filament\Widgets\RecentMovementsWidget::class)

            </div>
        @endif

        {{-- ===== ANALYTICS AVANZATE TAB ===== --}}
        @if ($tab === 'analytics')
            <div class="space-y-6">

                {{-- Confronto performance mensile --}}
                @livewire(\App\Filament\Widgets\PerformanceComparisonWidget::class)

                {{-- Previsione fatturato --}}
                @livewire(\App\Filament\Widgets\SalesForecastChart::class)

                {{-- Trend per categoria + trend per marca --}}
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    @livewire(\App\Filament\Widgets\SalesByCategoryChart::class)
                    @livewire(\App\Filament\Widgets\SalesByBrandChart::class)
                </div>

                {{-- Analisi ABC --}}
                @livewire(\App\Filament\Widgets\AbcAnalysisWidget::class)

                {{-- Suggerimenti riordino --}}
                @livewire(\App\Filament\Widgets\ReorderSuggestionsWidget::class)

            </div>
        @endif

    </div>
</x-filament-panels::page>
