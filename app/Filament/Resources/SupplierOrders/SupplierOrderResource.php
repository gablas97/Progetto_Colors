<?php

namespace App\Filament\Resources\SupplierOrders;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SupplierOrder;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class SupplierOrderResource extends Resource
{
    protected static ?string $model = SupplierOrder::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static string|UnitEnum|null $navigationGroup = 'Fornitori';
    protected static ?string $navigationLabel = 'Ordini Fornitori';
    protected static ?string $modelLabel = 'Ordine Fornitore';
    protected static ?string $pluralModelLabel = 'Ordini Fornitori';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Ordine Fornitore')
                ->icon('heroicon-o-clipboard-document-check')->iconColor('primary')
                ->description('Fornitore e stato')
                ->aside()
                ->columnSpanFull()
                ->schema([
                    Forms\Components\Select::make('supplier_id')
                        ->label('Fornitore')
                        ->relationship('supplier', 'company_name')
                        ->required()
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('status')
                        ->label('Stato')
                        ->options([
                            'draft'              => 'Bozza',
                            'sent'               => 'Inviato',
                            'confirmed'          => 'Confermato',
                            'partially_received' => 'Ricevuto Parzialmente',
                            'received'           => 'Ricevuto',
                            'cancelled'          => 'Annullato',
                        ])
                        ->default('draft')
                        ->required(),
                    Forms\Components\DatePicker::make('expected_delivery_date')
                        ->label('Data Consegna Prevista')
                        ->hidden(fn (string $operation) => $operation === 'create'),
                    Forms\Components\DatePicker::make('received_date')
                        ->label('Data Ricezione')
                        ->hidden(fn (string $operation) => $operation === 'create'),
                ])->columns(2),
            Section::make('Prodotti')
                ->icon('heroicon-o-cube')->iconColor('primary')
                ->description('Articoli inclusi nell\'ordine')
                ->aside()
                ->columnSpanFull()
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship()
                        ->hiddenLabel()
                        ->schema([
                            Forms\Components\Select::make('product_id')
                                ->label('Prodotto')
                                ->options(fn () => Product::active()->pluck('name', 'id'))
                                ->required()
                                ->searchable()
                                ->live(),
                            Forms\Components\Select::make('product_variant_id')
                                ->label('Variante')
                                ->options(fn (Get $get) => $get('product_id')
                                    ? ProductVariant::where('product_id', $get('product_id'))->pluck('name', 'id')
                                    : [])
                                ->searchable()
                                ->live(),
                            Forms\Components\TextInput::make('quantity_ordered')->label('Qta Ordinata')->numeric()->required()->minValue(1),
                            Forms\Components\TextInput::make('quantity_received')->label('Qta Ricevuta')->numeric()->default(0)->minValue(0),
                            Forms\Components\TextInput::make('unit_price')->label('Prezzo Unitario')->numeric()->required()->prefix('€'),
                            Forms\Components\TextInput::make('total_price')->label('Totale')->numeric()->prefix('€')
                                ->disabled()
                                ->dehydrated(),
                        ])
                        ->columns(3)
                        ->addActionLabel('Aggiungi Prodotto')
                        ->reorderable(false),
                ]),
            Section::make('Importi e Note')
                ->icon('heroicon-o-currency-euro')->iconColor('success')
                ->description('Spedizione, IVA e note')
                ->aside()
                ->columnSpanFull()
                ->schema([
                    Forms\Components\TextInput::make('shipping_cost')
                        ->label('Costo Spedizione')
                        ->numeric()
                        ->default(0)
                        ->prefix('€'),
                    Forms\Components\TextInput::make('tax_rate')
                        ->label('Aliquota IVA (%)')
                        ->numeric()
                        ->default(22)
                        ->suffix('%')
                        ->minValue(0)
                        ->maxValue(100)
                        ->helperText('Default 22%'),
                    Forms\Components\Textarea::make('notes')->label('Note')->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Dettaglio Ordine')
                ->icon('heroicon-o-clipboard-document-check')->iconColor('primary')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('order_number')
                        ->label('N. Ordine')
                        ->size('lg')
                        ->weight('bold')
                        ->copyable(),
                    TextEntry::make('supplier.company_name')
                        ->label('Fornitore')
                        ->weight('medium'),
                    TextEntry::make('status')
                        ->label('Stato')
                        ->badge()
                        ->formatStateUsing(fn (string $state) => match ($state) {
                            'draft'              => 'Bozza',
                            'sent'               => 'Inviato',
                            'confirmed'          => 'Confermato',
                            'partially_received' => 'Parz. Ricevuto',
                            'received'           => 'Ricevuto',
                            'cancelled'          => 'Annullato',
                            default              => $state,
                        })
                        ->color(fn (string $state) => match ($state) {
                            'draft'              => 'gray',
                            'sent'               => 'info',
                            'confirmed'          => 'warning',
                            'partially_received' => 'primary',
                            'received'           => 'success',
                            'cancelled'          => 'danger',
                            default              => 'gray',
                        }),
                    TextEntry::make('expected_delivery_date')
                        ->label('Consegna Prevista')
                        ->date('d/m/Y')
                        ->placeholder('—'),
                    TextEntry::make('received_date')
                        ->label('Data Ricezione')
                        ->date('d/m/Y')
                        ->placeholder('—'),
                    TextEntry::make('createdBy.name')
                        ->label('Creato da')
                        ->placeholder('—'),
                ])->columns(3),

            Section::make('Prodotti Ordinati')
                ->icon('heroicon-o-cube')->iconColor('primary')
                ->columnSpanFull()
                ->schema([
                    RepeatableEntry::make('items')
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('product.name')->label('Prodotto'),
                            TextEntry::make('productVariant.name')->label('Variante')->placeholder('—'),
                            TextEntry::make('quantity_ordered')->label('Qta Ordinata'),
                            TextEntry::make('quantity_received')->label('Qta Ricevuta'),
                            TextEntry::make('unit_price')->label('Prezzo Unit.')->money('EUR')->color('gray'),
                            TextEntry::make('total_price')->label('Totale')->money('EUR')->weight('bold'),
                        ])
                        ->columns(6)
                        ->columnSpanFull(),
                ]),

            Section::make('Importi')
                ->icon('heroicon-o-currency-euro')->iconColor('success')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('subtotal')->label('Subtotale')->money('EUR')->color('gray'),
                    TextEntry::make('tax_amount')
                        ->label('IVA')
                        ->color('gray')
                        ->formatStateUsing(fn ($state, SupplierOrder $record): string =>
                            '€ ' . number_format((float) $state, 2, ',', '.') . ' (' . number_format($record->tax_rate * 100, 0) . '%)'
                        ),
                    TextEntry::make('shipping_cost')->label('Spedizione')->money('EUR')->color('gray'),
                    TextEntry::make('total')->label('Totale')->money('EUR')->weight('bold')->size('lg')->color('success'),
                ])->columns(4),

            Section::make('Note')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')->iconColor('gray')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('notes')->hiddenLabel()->placeholder('Nessuna nota')->columnSpanFull(),
                ])->collapsible()->collapsed(fn (SupplierOrder $record) => !$record->notes),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->label('N. Ordine')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('supplier.company_name')->label('Fornitore')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Stato')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'draft'              => 'Bozza',
                        'sent'               => 'Inviato',
                        'confirmed'          => 'Confermato',
                        'partially_received' => 'Parz. Ricevuto',
                        'received'           => 'Ricevuto',
                        'cancelled'          => 'Annullato',
                        default              => $state,
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'draft'              => 'gray',
                        'sent'               => 'info',
                        'confirmed'          => 'warning',
                        'partially_received' => 'primary',
                        'received'           => 'success',
                        'cancelled'          => 'danger',
                        default              => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total')->label('Totale')->money('EUR')->sortable(),
                Tables\Columns\TextColumn::make('expected_delivery_date')->label('Consegna Prevista')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Creato il')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Stato')
                    ->options([
                        'draft'              => 'Bozza',
                        'sent'               => 'Inviato',
                        'confirmed'          => 'Confermato',
                        'partially_received' => 'Parz. Ricevuto',
                        'received'           => 'Ricevuto',
                        'cancelled'          => 'Annullato',
                    ]),
                Tables\Filters\SelectFilter::make('supplier_id')
                    ->label('Fornitore')
                    ->relationship('supplier', 'company_name'),
                Tables\Filters\TrashedFilter::make()
                    ->label('Mostra Eliminati')
                    ->placeholder('No')
                    ->trueLabel('Si')
                    ->falseLabel('Solo Eliminati'),
            ])
            ->recordUrl(null)
            ->recordAction(ViewAction::class)
            ->recordActions([
                ViewAction::make()
                    ->extraAttributes(['style' => 'display:none'])
                    ->modalWidth('4xl'),
                ActionGroup::make([
                    EditAction::make()->label('Modifica')->color('warning'),
                    Action::make('change_status')
                        ->label('Cambia Stato')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->modalHeading('Cambia Stato Ordine')
                        ->modalDescription(fn (SupplierOrder $record): string =>
                            'Stato attuale: ' . match ($record->status) {
                                'draft'              => 'Bozza',
                                'sent'               => 'Inviato',
                                'confirmed'          => 'Confermato',
                                'partially_received' => 'Ricevuto Parzialmente',
                                'received'           => 'Ricevuto',
                                'cancelled'          => 'Annullato',
                                default              => $record->status,
                            }
                        )
                        ->modalAlignment('center')
                        ->modalWidth('md')
                        ->modalIcon('heroicon-o-arrow-path')
                        ->modalIconColor('info')
                        ->modalSubmitActionLabel('Aggiorna Stato')
                        ->schema([
                            Forms\Components\Hidden::make('current_status')
                                ->default(fn (SupplierOrder $record) => $record->status),

                            Forms\Components\Select::make('status')
                                ->label('Nuovo Stato')
                                ->options([
                                    'draft'              => 'Bozza',
                                    'sent'               => 'Inviato',
                                    'confirmed'          => 'Confermato',
                                    'partially_received' => 'Ricevuto Parzialmente',
                                    'received'           => 'Ricevuto',
                                    'cancelled'          => 'Annullato',
                                ])
                                ->default(fn (SupplierOrder $record) => $record->status)
                                ->selectablePlaceholder(false)
                                ->required()
                                ->live()
                                ->disableOptionWhen(function (string $value, Get $get): bool {
                                    $rank = [
                                        'draft'              => 0,
                                        'sent'               => 1,
                                        'confirmed'          => 2,
                                        'partially_received' => 3,
                                        'received'           => 4,
                                    ];
                                    $current = $get('current_status');
                                    // Annullato: disponibile solo se non già ricevuto/annullato
                                    if ($value === 'cancelled') {
                                        return in_array($current, ['received', 'cancelled']);
                                    }
                                    // Blocca qualsiasi stato con rank inferiore a quello attuale
                                    return ($rank[$value] ?? 0) < ($rank[$current] ?? 0);
                                }),

                            Forms\Components\DatePicker::make('expected_delivery_date')
                                ->label('Data Consegna Prevista')
                                ->hidden(fn (Get $get) => $get('status') !== 'confirmed')
                                ->helperText('Quando è prevista la consegna da parte del fornitore?'),

                            Forms\Components\DatePicker::make('received_date')
                                ->label('Data Ricezione')
                                ->hidden(fn (Get $get) => !in_array($get('status'), ['partially_received', 'received']))
                                ->default(now())
                                ->helperText('Data in cui è avvenuta la ricezione della merce'),
                        ])
                        ->action(function (SupplierOrder $record, array $data): void {
                            $update = ['status' => $data['status']];
                            if (!empty($data['expected_delivery_date'])) {
                                $update['expected_delivery_date'] = $data['expected_delivery_date'];
                            }
                            if (!empty($data['received_date'])) {
                                $update['received_date'] = $data['received_date'];
                            }
                            $record->update($update);
                        }),
                    RestoreAction::make()->label('Ripristina'),
                    DeleteAction::make()
                        ->label('Elimina')
                        ->visible(fn (SupplierOrder $record) => $record->status === 'draft')
                        ->modalHeading('Elimina ordine fornitore')
                        ->modalDescription('L\'ordine verrà eliminato definitivamente. Continuare?'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()->label('Ripristina selezionati'),
                    DeleteBulkAction::make()->label('Elimina selezionati'),
                ])->label('Azioni'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSupplierOrders::route('/'),
            'create' => Pages\CreateSupplierOrder::route('/create'),
            'edit'   => Pages\EditSupplierOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                \Illuminate\Database\Eloquent\SoftDeletingScope::class,
            ]);
    }
}
