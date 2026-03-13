<?php

namespace App\Filament\Resources\Inventory;

use App\Filament\Resources\Inventory\Pages;
use App\Models\Product;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class InventoryResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|UnitEnum|null $navigationGroup = 'Magazzino';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Inventario';

    protected static ?string $pluralModelLabel = 'Inventario';

    public static function schema(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('is_active', true)
                    ->where('manage_stock', true)
                    ->with(['brand', 'categories'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('medium'),

                Tables\Columns\ImageColumn::make('main_image')
                    ->label('')
                    ->square()
                    ->size(40),

                Tables\Columns\TextColumn::make('name')
                    ->label('Prodotto')
                    ->searchable()
                    ->sortable()
                    ->limit(35)
                    ->description(fn (Product $record) => $record->brand?->name),

                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Categoria')
                    ->badge()
                    ->separator(',')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Giacenza')
                    ->sortable()
                    ->badge()
                    ->color(fn (Product $record) => match (true) {
                        $record->stock_quantity <= 0                                  => 'danger',
                        $record->stock_quantity <= $record->low_stock_threshold       => 'warning',
                        default                                                        => 'success',
                    })
                    ->formatStateUsing(fn (Product $record) =>
                        $record->stock_quantity . ' pz'
                        . ($record->stock_quantity <= $record->low_stock_threshold && $record->stock_quantity > 0 ? ' ⚠' : '')
                        . ($record->stock_quantity <= 0 ? ' ✕' : '')
                    ),

                Tables\Columns\TextColumn::make('low_stock_threshold')
                    ->label('Soglia')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('stock_value')
                    ->label('Valore Stock')
                    ->sortable(query: fn (Builder $query, string $direction) =>
                        $query->orderByRaw("stock_quantity * COALESCE(cost, price) {$direction}")
                    )
                    ->getStateUsing(fn (Product $record) =>
                        number_format($record->stock_quantity * ($record->cost ?? $record->price), 2) . ' €'
                    )
                    ->description(fn (Product $record) =>
                        'Costo: ' . number_format($record->cost ?? 0, 2) . ' €'
                    )
                    ->toggleable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prezzo')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('stock_quantity', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('brand')
                    ->label('Brand')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('categories')
                    ->label('Categoria')
                    ->relationship('categories', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('low_stock')
                    ->label('Solo Stock Basso')
                    ->query(fn (Builder $query) => $query->lowStock()),

                Tables\Filters\Filter::make('out_of_stock')
                    ->label('Solo Esauriti')
                    ->query(fn (Builder $query) => $query->outOfStock()),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->label('Esporta Excel')
                    ->icon('heroicon-o-arrow-down-tray'),
            ])
            ->toolbarActions([
                Tables\Actions\ExportBulkAction::make()
                    ->label('Esporta Selezionati'),
            ])
            ->emptyStateHeading('Nessun prodotto con gestione stock attiva')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventory::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
