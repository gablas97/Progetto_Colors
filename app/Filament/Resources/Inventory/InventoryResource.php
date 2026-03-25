<?php

namespace App\Filament\Resources\Inventory;

use App\Exports\InventoryExporter;
use App\Filament\Resources\Inventory\Pages;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use UnitEnum;

class InventoryResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|UnitEnum|null $navigationGroup = 'Magazzino';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'inventory';

    protected static ?string $modelLabel = 'Inventario';

    protected static ?string $pluralModelLabel = 'Inventario';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Prodotto')
                ->icon('heroicon-o-cube')->iconColor('primary')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('sku')
                        ->label('SKU')
                        ->badge()
                        ->color('gray')
                        ->copyable()
                        ->placeholder('—'),
                    TextEntry::make('name')
                        ->label('Nome Prodotto')
                        ->weight('bold'),
                    TextEntry::make('brand.name')
                        ->label('Brand')
                        ->color('primary')
                        ->placeholder('—'),
                    TextEntry::make('categories.name')
                        ->label('Categorie')
                        ->badge()
                        ->separator(','),
                ])->columns(2),

            Section::make('Stock')
                ->icon('heroicon-o-archive-box')->iconColor('warning')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('stock_quantity')
                        ->label('Giacenza Attuale')
                        ->color(fn (Product $record): string => match (true) {
                            $record->stock_quantity <= 0                            => 'danger',
                            $record->stock_quantity <= $record->low_stock_threshold => 'warning',
                            default                                                 => 'success',
                        })
                        ->formatStateUsing(fn (Product $record): string => $record->stock_quantity . ' pz'),

                    TextEntry::make('low_stock_threshold')
                        ->label('Soglia Riordino')
                        ->formatStateUsing(fn ($state): string => $state . ' pz'),

                    TextEntry::make('price')
                        ->label('Prezzo di Vendita')
                        ->money('EUR'),

                    TextEntry::make('cost')
                        ->label('Costo di Acquisto')
                        ->money('EUR')
                        ->placeholder('—'),
                ])->columns(2),

            Section::make('Valore Stock')
                ->icon('heroicon-o-currency-euro')->iconColor('success')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('stock_value_calculated')
                        ->label('Valore a Prezzo di Vendita')
                        ->getStateUsing(fn (Product $record): string =>
                            number_format($record->stock_quantity * $record->price, 2, ',', '.') . ' €'
                        ),
                    TextEntry::make('stock_cost_calculated')
                        ->label('Valore a Costo')
                        ->getStateUsing(fn (Product $record): string =>
                            $record->cost
                                ? number_format($record->stock_quantity * $record->cost, 2, ',', '.') . ' €'
                                : '—'
                        ),
                ])->columns(2),
        ]);
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
                    ->copyMessage('SKU copiato!')
                    ->tooltip('Clicca per copiare')
                    ->weight('medium')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\ImageColumn::make('main_image')
                    ->label('Immagine')
                    ->disk('public')
                    ->square()
                    ->imageSize(40)
                    ->toggleable()
                    ->placeholder('—')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Prodotto')
                    ->searchable()
                    ->sortable()
                    ->limit(35)
                    ->toggleable()
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
                    )
                    ->toggleable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('low_stock_threshold')
                    ->label('Soglia')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state . ' pz')
                    ->toggleable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('stock_value')
                    ->label('Valore Stock')
                    ->sortable(query: fn (Builder $query, string $direction) =>
                        $query->orderByRaw("stock_quantity * COALESCE(cost, price) {$direction}")
                    )
                    ->getStateUsing(fn (Product $record) =>
                        number_format($record->stock_quantity * ($record->price), 2, ',', '.') . ' €'
                    )
                    ->toggleable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prezzo')
                    ->description(fn (Product $record) =>
                        'Costo: ' . number_format($record->cost ?? '—', 2, ',', '.') . ' €'
                    )
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
                Action::make('export')
                    ->label('Esporta Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn (): BinaryFileResponse => Excel::download(
                        new InventoryExporter(),
                        'INVENTARIO-' . now()->format('Y-m-d') . '.xlsx'
                    )),
            ])
            ->recordUrl(null)
            ->recordAction(ViewAction::class)
            ->recordActions([
                ViewAction::make()
                    ->extraAttributes(['style' => 'display:none'])
                    ->modalWidth('3xl'),
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
