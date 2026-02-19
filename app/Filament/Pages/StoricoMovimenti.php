<?php


namespace App\Filament\Pages;

use BackedEnum;
use UnitEnum;
use App\Models\WarehouseMovement;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class StoricoMovimenti extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';
    protected static string|UnitEnum|null $navigationGroup = 'Magazzino';
    protected static ?string $navigationLabel = 'Storico Movimenti';
    protected static ?int $navigationSort = 4;
    protected static ?string $title = 'Storico Movimenti Magazzino';
    protected string $view = 'filament.pages.storico-movimenti';

    public function table(Table $table): Table
    {
        return $table
            ->query(WarehouseMovement::query()->with(['product', 'productVariant', 'user']))
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match($state) {
                        'carico' => 'Carico', 'scarico' => 'Scarico', 'reso' => 'Reso', default => $state,
                    })
                    ->color(fn (string $state) => match($state) {
                        'carico' => 'success', 'scarico' => 'danger', 'reso' => 'warning', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Causale')
                    ->formatStateUsing(fn ($record) => $record->getReasonLabel()),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Prodotto')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('productVariant.name')
                    ->label('Variante')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qtà')
                    ->formatStateUsing(fn ($state) => ($state > 0 ? '+' : '') . $state)
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('stock_before')
                    ->label('Prima')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('stock_after')
                    ->label('Dopo')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Riferimento')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user.first_name')
                    ->label('Operatore')
                    ->formatStateUsing(fn ($record) => $record->user?->full_name ?? 'Sistema')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(['carico' => 'Carico', 'scarico' => 'Scarico', 'reso' => 'Reso']),
                Tables\Filters\SelectFilter::make('reason')
                    ->label('Causale')
                    ->options([
                        'acquisto_fornitore' => 'Acquisto Fornitore',
                        'vendita_online' => 'Vendita Online',
                        'vendita_negozio' => 'Vendita Negozio',
                        'reso_cliente' => 'Reso Cliente',
                        'reso_fornitore' => 'Reso Fornitore',
                        'inventario' => 'Inventario',
                        'aggiustamento' => 'Aggiustamento',
                        'danneggiamento' => 'Danneggiamento',
                        'omaggio' => 'Omaggio',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100]);
    }
}
