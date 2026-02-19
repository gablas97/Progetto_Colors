<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Widgets\TableWidget;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;

class PopularProducts extends TableWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Prodotti Più Venduti')
            ->query(fn (): Builder => Product::query()
                ->where('sales_count', '>', 0)
                ->orderBy('sales_count', 'desc')
                ->limit(10)
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
                    ->toggleable(),

                TextColumn::make('sales_count')
                    ->label('Vendite')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),

                TextColumn::make('price')
                    ->label('Prezzo')
                    ->money('EUR'),

                TextColumn::make('average_rating')
                    ->label('Valutazione')
                    ->formatStateUsing(fn ($state): string => $state > 0 ? number_format($state, 1) . ' ⭐' : 'N/A')
                    ->color('warning'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Visualizza')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Product $record): string => route('filament.admin.resources.products.edit', $record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ])
            ->paginated(false);
    }
}
