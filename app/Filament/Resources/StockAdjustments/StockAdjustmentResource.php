<?php

namespace App\Filament\Resources\StockAdjustments;

use App\Filament\Resources\StockAdjustments\Pages;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockLog;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;

class StockAdjustmentResource extends Resource
{
    protected static ?string $model = StockLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static string|UnitEnum|null $navigationGroup = 'Magazzino';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Movimento Stock';

    protected static ?string $pluralModelLabel = 'Movimenti Stock';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Movimento Stock')
                ->icon('heroicon-o-arrows-right-left')->iconColor('primary')
                ->description('Seleziona il prodotto e specifica il tipo e la quantità del movimento')
                ->aside()
                ->columnSpanFull()
                ->schema([
                    Forms\Components\Select::make('product_id')
                        ->label('Prodotto')
                        ->options(Product::where('is_active', true)->where('manage_stock', true)->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn ($set) => $set('product_variant_id', null)),

                    Forms\Components\Select::make('product_variant_id')
                        ->label('Variante (opzionale)')
                        ->options(fn (Get $get) => ProductVariant::where('product_id', $get('product_id'))
                            ->where('is_active', true)
                            ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->placeholder('Nessuna variante')
                        ->hidden(fn (Get $get) => !$get('product_id')),

                    Forms\Components\Radio::make('type')
                        ->label('Tipo Operazione')
                        ->options([
                            'manual_load'   => 'Carico (aumenta stock)',
                            'manual_unload' => 'Scarico (diminuisce stock)',
                        ])
                        ->required()
                        ->inline()
                        ->default('manual_load')
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('quantity')
                        ->label('Quantità')
                        ->numeric()
                        ->integer()
                        ->minValue(1)
                        ->required()
                        ->default(1),

                    Forms\Components\Textarea::make('reason')
                        ->label('Motivo')
                        ->rows(2)
                        ->placeholder('Descrivi il motivo del movimento...')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'manual_load' => 'Carico Manuale',
                        'manual_unload' => 'Scarico Manuale',
                        'order_fulfilled' => 'Ordine Evaso',
                        'supplier_order_received' => 'Carico Fornitore',
                        'return' => 'Reso',
                        default         => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'manual_load', 'supplier_order_received', 'return' => 'success',
                        'manual_unload', 'order_fulfilled' => 'warning',
                        default         => 'gray',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Prodotto')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('productVariant.name')
                    ->label('Variante')
                    ->placeholder('-')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantità')
                    ->numeric()
                    ->alignCenter()
                    ->color(fn ($record) => in_array($record->type, ['manual_load', 'supplier_order_received', 'return']) ? 'success' : 'warning')
                    ->formatStateUsing(fn ($record) => 
                        in_array($record->type, ['manual_load', 'supplier_order_received', 'return']) 
                            ? '+' . $record->quantity 
                            : '-' . $record->quantity
                    )
                    ->toggleable(),

                Tables\Columns\TextColumn::make('quantity_before')
                    ->label('Prima')
                    ->numeric()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('quantity_after')
                    ->label('Dopo')
                    ->numeric()
                    ->alignCenter()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Motivo')
                    ->limit(40)
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Operatore')
                    ->placeholder('Sistema')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cancelled_at')
                    ->label('Stato')
                    ->badge()
                    ->getStateUsing(fn (StockLog $record): string => $record->cancelled_at ? 'Annullato' : 'Eseguito')
                    ->color(fn (string $state): string => $state === 'Annullato' ? 'danger' : 'success'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo Movimento')
                    ->options([
                        'manual_load' => 'Carico Manuale',
                        'manual_unload' => 'Scarico Manuale',
                        'order_fulfilled' => 'Ordine Evaso',
                        'supplier_order_received' => 'Carico Fornitore',
                        'return' => 'Reso',
                    ]),

                Tables\Filters\SelectFilter::make('product_id')
                ->label('Prodotto')
                ->relationship('product', 'name')
                ->searchable()
                ->preload(),

                Tables\Filters\Filter::make('created_at')
                    ->schema([
                        Forms\Components\DatePicker::make('from')->label('Dal'),
                        Forms\Components\DatePicker::make('until')->label('Al'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
                            ->when($data['until'], fn ($q, $v) => $q->whereDate('created_at', '<=', $v));
                    }),
            ])
            ->recordUrl(null)
            ->recordAction(ViewAction::class)
            ->recordActions([
                ViewAction::make()
                    ->extraAttributes(['style' => 'display:none'])
                    ->modalWidth('2xl'),
                ActionGroup::make([
                    Action::make('cancel_movement')
                        ->label('Annulla movimento')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Annulla movimento')
                        ->modalDescription('Lo stock verrà ripristinato automaticamente. Il movimento rimarrà visibile come "Annullato" per l\'audit trail.')
                        ->visible(fn (StockLog $record) => ! $record->isCancelled())
                        ->action(function (StockLog $record) {
                            $loadTypes = ['manual_load', 'supplier_order_received', 'return'];
                            $isLoad    = in_array($record->type, $loadTypes);

                            // Ripristina lo stock
                            if ($record->product_variant_id) {
                                $target = $record->productVariant;
                                if ($target) {
                                    $isLoad
                                        ? $target->decrement('stock_quantity', $record->quantity)
                                        : $target->increment('stock_quantity', $record->quantity);

                                    $stockAfter = $target->fresh()->stock_quantity;
                                }
                            } else {
                                $target = $record->product;
                                if ($target && $target->manage_stock) {
                                    $isLoad
                                        ? $target->decrement('stock_quantity', $record->quantity)
                                        : $target->increment('stock_quantity', $record->quantity);

                                    $stockAfter = $target->fresh()->stock_quantity;
                                }
                            }

                            // Crea movimento inverso di tracciamento
                            StockLog::create([
                                'product_id'         => $record->product_id,
                                'product_variant_id' => $record->product_variant_id,
                                'type'               => $isLoad ? 'manual_unload' : 'manual_load',
                                'quantity'           => $record->quantity,
                                'quantity_before'    => $record->quantity_after,
                                'quantity_after'     => $stockAfter ?? $record->quantity_before,
                                'reason'             => 'Annullamento movimento #' . $record->id,
                                'user_id'            => auth()->id(),
                            ]);

                            // Marca il movimento originale come annullato
                            $record->update([
                                'cancelled_at' => now(),
                                'cancelled_by' => auth()->id(),
                            ]);
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_cancel')
                        ->label('Annulla selezionati')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Annulla movimenti selezionati')
                        ->modalDescription('Lo stock verrà ripristinato per ciascun movimento. I movimenti rimarranno visibili come "Annullati" per l\'audit trail.')
                        ->action(function (Collection $records) {
                            $loadTypes = ['manual_load', 'supplier_order_received', 'return'];

                            foreach ($records as $record) {
                                if ($record->isCancelled()) continue;

                                $isLoad = in_array($record->type, $loadTypes);

                                if ($record->product_variant_id) {
                                    $target = $record->productVariant;
                                    if ($target) {
                                        $isLoad
                                            ? $target->decrement('stock_quantity', $record->quantity)
                                            : $target->increment('stock_quantity', $record->quantity);
                                        $stockAfter = $target->fresh()->stock_quantity;
                                    }
                                } else {
                                    $target = $record->product;
                                    if ($target && $target->manage_stock) {
                                        $isLoad
                                            ? $target->decrement('stock_quantity', $record->quantity)
                                            : $target->increment('stock_quantity', $record->quantity);
                                        $stockAfter = $target->fresh()->stock_quantity;
                                    }
                                }

                                StockLog::create([
                                    'product_id'         => $record->product_id,
                                    'product_variant_id' => $record->product_variant_id,
                                    'type'               => $isLoad ? 'manual_unload' : 'manual_load',
                                    'quantity'           => $record->quantity,
                                    'quantity_before'    => $record->quantity_after,
                                    'quantity_after'     => $stockAfter ?? $record->quantity_before,
                                    'reason'             => 'Annullamento movimento #' . $record->id,
                                    'user_id'            => auth()->id(),
                                ]);

                                $record->update([
                                    'cancelled_at' => now(),
                                    'cancelled_by' => auth()->id(),
                                ]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateHeading('Nessun movimento')
            ->emptyStateDescription('Usa il bottone "Nuovo Movimento" per caricare o scaricare stock manualmente.')
            ->emptyStateIcon('heroicon-o-arrows-right-left');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Dettaglio Movimento')
                ->icon('heroicon-o-arrows-right-left')->iconColor('primary')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Data e Ora')
                        ->dateTime('d/m/Y H:i'),

                    TextEntry::make('type')
                        ->label('Tipo')
                        ->badge()
                        ->formatStateUsing(fn (string $state) => match ($state) {
                            'manual_load'             => 'Carico Manuale',
                            'manual_unload'           => 'Scarico Manuale',
                            'order_fulfilled'         => 'Ordine Evaso',
                            'supplier_order_received' => 'Carico Fornitore',
                            'return'                  => 'Reso',
                            default                   => $state,
                        })
                        ->color(fn (string $state) => match ($state) {
                            'manual_load', 'supplier_order_received', 'return' => 'success',
                            'manual_unload', 'order_fulfilled'                 => 'warning',
                            default                                            => 'gray',
                        }),

                    TextEntry::make('product.name')
                        ->label('Prodotto')
                        ->weight('medium'),

                    TextEntry::make('productVariant.name')
                        ->label('Variante')
                        ->placeholder('—')
                        ->color('gray'),

                    TextEntry::make('quantity')
                        ->label('Quantità')
                        ->formatStateUsing(fn ($state, StockLog $record): string =>
                            in_array($record->type, ['manual_load', 'supplier_order_received', 'return'])
                                ? '+' . $state
                                : '-' . $state
                        )
                        ->color(fn (StockLog $record): string =>
                            in_array($record->type, ['manual_load', 'supplier_order_received', 'return'])
                                ? 'success'
                                : 'warning'
                        )
                        ->weight('bold'),

                    TextEntry::make('user.name')
                        ->label('Operatore')
                        ->placeholder('Sistema'),
                ])->columns(2),

            Section::make('Stock')
                ->icon('heroicon-o-archive-box')->iconColor('warning')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('quantity_before')
                        ->label('Stock Prima')
                        ->color('gray'),
                    TextEntry::make('quantity_after')
                        ->label('Stock Dopo')
                        ->color(fn ($state): string => match (true) {
                            $state == 0  => 'danger',
                            $state <= 10 => 'warning',
                            default      => 'success',
                        }),
                ])->columns(2),

            Section::make('Motivo')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')->iconColor('gray')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('reason')
                        ->hiddenLabel()
                        ->placeholder('Nessun motivo specificato')
                        ->columnSpanFull(),
                ])->collapsible()->collapsed(fn (StockLog $record) => !$record->reason),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStockAdjustments::route('/'),
            'create' => Pages\CreateStockAdjustment::route('/create'),
        ];
    }
}
