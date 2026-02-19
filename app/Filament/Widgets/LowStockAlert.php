<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Widgets\TableWidget;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;

class LowStockAlert extends TableWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('⚠️ Alert Stock Basso')
            ->query(fn (): Builder => Product::query()
                ->where('manage_stock', true)
                ->where(function ($query) {
                    $query->where('stock_quantity', '<=', 'low_stock_threshold')
                        ->orWhere('stock_quantity', 0);
                })
                ->orderBy('stock_quantity', 'asc')
            )
            ->columns([
                ImageColumn::make('main_image')
                    ->label('Immagine')
                    ->square()
                    ->defaultImageUrl(url('/images/placeholder-product.png')),

                TextColumn::make('name')
                    ->label('Prodotto')
                    ->searchable()
                    ->weight('medium'),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('stock_quantity')
                    ->label('Quantità')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state <= 5 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    })
                    ->suffix(' pz'),

                TextColumn::make('low_stock_threshold')
                    ->label('Soglia')
                    ->badge()
                    ->color('gray')
                    ->suffix(' pz'),

                TextColumn::make('sales_count')
                    ->label('Vendite')
                    ->badge()
                    ->color('info'),

                IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Modifica Stock')
                    ->icon('heroicon-o-pencil')
                    ->url(fn (Product $record): string => route('filament.admin.resources.products.edit', $record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ])
            ->emptyStateHeading('✅ Nessun prodotto in esaurimento')
            ->emptyStateDescription('Tutti i prodotti hanno stock sufficiente')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->paginated([5, 10, 25]);
    }
}
