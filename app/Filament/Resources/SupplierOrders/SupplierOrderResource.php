<?php


namespace App\Filament\Resources\SupplierOrders;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SupplierOrder;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
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
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string|UnitEnum|null $navigationGroup = 'Fornitori';
    protected static ?string $navigationLabel = 'Ordini Fornitori';
    protected static ?string $modelLabel = 'Ordine Fornitore';
    protected static ?string $pluralModelLabel = 'Ordini Fornitori';
    protected static ?int $navigationSort = 2;

    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Ordine Fornitore')
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
                            'draft' => 'Bozza',
                            'sent' => 'Inviato',
                            'confirmed' => 'Confermato',
                            'partially_received' => 'Ricevuto Parzialmente',
                            'received' => 'Ricevuto',
                            'cancelled' => 'Annullato',
                        ])
                        ->default('draft')
                        ->required(),
                    Forms\Components\DatePicker::make('expected_delivery_date')->label('Data Consegna Prevista'),
                    Forms\Components\DatePicker::make('received_date')->label('Data Ricezione'),
                ])->columns(2),
            Section::make('Prodotti')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('product_id')
                                ->label('Prodotto')
                                ->options(fn() => Product::active()->pluck('name', 'id'))
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
                ->schema([
                    Forms\Components\TextInput::make('shipping_cost')->label('Costo Spedizione')->numeric()->default(0)->prefix('€'),
                    Forms\Components\Textarea::make('notes')->label('Note')->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->label('N. Ordine')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('supplier.company_name')->label('Fornitore')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Stato')
                    ->formatStateUsing(fn (string $state) => match($state) {
                        'draft' => 'Bozza',
                        'sent' => 'Inviato',
                        'confirmed' => 'Confermato',
                        'partially_received' => 'Parz. Ricevuto',
                        'received' => 'Ricevuto',
                        'cancelled' => 'Annullato',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'draft' => 'gray',
                        'sent' => 'info',
                        'confirmed' => 'warning',
                        'partially_received' => 'primary',
                        'received' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total')->label('Totale')->money('EUR')->sortable(),
                Tables\Columns\TextColumn::make('expected_delivery_date')->label('Consegna Prevista')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Creato il')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Stato')
                    ->options([
                        'draft' => 'Bozza',
                        'sent' => 'Inviato',
                        'confirmed' => 'Confermato',
                        'partially_received' => 'Parz. Ricevuto',
                        'received' => 'Ricevuto',
                        'cancelled' => 'Annullato',
                    ]),
                Tables\Filters\SelectFilter::make('supplier_id')
                    ->label('Fornitore')
                    ->relationship('supplier', 'company_name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupplierOrders::route('/'),
            'create' => Pages\CreateSupplierOrder::route('/create'),
            'edit' => Pages\EditSupplierOrder::route('/{record}/edit'),
            'view' => Pages\ViewSupplierOrder::route('/{record}'),
        ];
    }
}
