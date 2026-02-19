<?php


namespace App\Filament\Pages;

use BackedEnum;
use UnitEnum;
use App\Models\Product;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class Inventario extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string|UnitEnum|null $navigationGroup = 'Magazzino';
    protected static ?string $navigationLabel = 'Inventario';
    protected static ?int $navigationSort = 5;
    protected static ?string $title = 'Inventario Prodotti';
    protected string $view = 'filament.pages.inventario';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('is_active', true)
                    ->where('manage_stock', true)
                    ->with(['brand', 'categories', 'variants'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('barcode')
                    ->label('Codice a Barre')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Prodotto')
                    ->searchable()
                    ->sortable()
                    ->limit(35),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Marca')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Giacenza')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => match(true) {
                        $record->stock_quantity <= 0 => 'danger',
                        $record->stock_quantity <= $record->low_stock_threshold => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('low_stock_threshold')
                    ->label('Soglia Min.')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('cost')
                    ->label('Costo')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Prezzo')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_value')
                    ->label('Valore Giacenza')
                    ->getStateUsing(fn ($record) => $record->stock_quantity * ($record->cost ?? $record->price))
                    ->money('EUR')
                    ->sortable(query: fn ($query, $direction) => $query->orderByRaw("stock_quantity * COALESCE(cost, price) {$direction}")),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('stock_status')
                    ->label('Stato Stock')
                    ->options([
                        'out_of_stock' => 'Esaurito',
                        'low_stock' => 'In esaurimento',
                        'in_stock' => 'Disponibile',
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value'] ?? null) {
                            'out_of_stock' => $query->where('stock_quantity', '<=', 0),
                            'low_stock' => $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')->where('stock_quantity', '>', 0),
                            'in_stock' => $query->whereColumn('stock_quantity', '>', 'low_stock_threshold'),
                            default => $query,
                        };
                    }),
                Tables\Filters\SelectFilter::make('brand_id')
                    ->label('Marca')
                    ->relationship('brand', 'name'),
            ])
            ->defaultSort('name')
            ->paginated([25, 50, 100]);
    }
}
