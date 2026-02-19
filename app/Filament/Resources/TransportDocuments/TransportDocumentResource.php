<?php


namespace App\Filament\Resources\TransportDocuments;

use App\Models\TransportDocument;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class TransportDocumentResource extends Resource
{
    protected static ?string $model = TransportDocument::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';
    protected static string|UnitEnum|null $navigationGroup = 'DDT';
    protected static ?string $navigationLabel = 'Documenti di Trasporto';
    protected static ?string $modelLabel = 'DDT';
    protected static ?string $pluralModelLabel = 'Documenti di Trasporto';
    protected static ?int $navigationSort = 1;

    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dati DDT')
                ->schema([
                    Forms\Components\TextInput::make('document_number')->label('Numero DDT')
                        ->disabled()->dehydrated()->helperText('Generato automaticamente'),
                    Forms\Components\Select::make('order_id')->label('Ordine Collegato')
                        ->relationship('order', 'order_number')->searchable()->preload(),
                    Forms\Components\Select::make('status')->label('Stato')
                        ->options(['draft' => 'Bozza', 'ready' => 'Pronto', 'shipped' => 'Spedito', 'delivered' => 'Consegnato'])
                        ->default('draft'),
                    Forms\Components\Select::make('reason')->label('Causale')
                        ->options([
                            'vendita' => 'Vendita', 'reso' => 'Reso',
                            'conto_lavorazione' => 'Conto Lavorazione',
                            'omaggio' => 'Omaggio', 'riparazione' => 'Riparazione', 'altro' => 'Altro',
                        ])->default('vendita')->required(),
                ])->columns(2),
            Section::make('Mittente')
                ->schema([
                    Forms\Components\TextInput::make('sender_name')->label('Mittente')->default('Colors S.r.l.'),
                    Forms\Components\TextInput::make('sender_address')->label('Indirizzo'),
                    Forms\Components\TextInput::make('sender_city')->label('Città'),
                    Forms\Components\TextInput::make('sender_province')->label('Prov.')->maxLength(2),
                    Forms\Components\TextInput::make('sender_postal_code')->label('CAP'),
                ])->columns(5)->collapsible(),
            Section::make('Destinatario')
                ->schema([
                    Forms\Components\TextInput::make('recipient_name')->label('Destinatario')->required(),
                    Forms\Components\TextInput::make('recipient_address')->label('Indirizzo')->required(),
                    Forms\Components\TextInput::make('recipient_city')->label('Città')->required(),
                    Forms\Components\TextInput::make('recipient_province')->label('Prov.')->maxLength(2)->required(),
                    Forms\Components\TextInput::make('recipient_postal_code')->label('CAP')->required(),
                ])->columns(5),
            Section::make('Trasporto')
                ->schema([
                    Forms\Components\Select::make('shipping_method')->label('Mezzo di Trasporto')
                        ->options(['corriere' => 'Corriere', 'ritiro' => 'Ritiro', 'proprio_mezzo' => 'Proprio Mezzo'])
                        ->default('corriere'),
                    Forms\Components\TextInput::make('carrier_name')->label('Corriere'),
                    Forms\Components\TextInput::make('tracking_number')->label('Tracking'),
                    Forms\Components\TextInput::make('packages_count')->label('N. Colli')->numeric()->default(1),
                    Forms\Components\TextInput::make('total_weight')->label('Peso Totale (kg)')->numeric(),
                    Forms\Components\TextInput::make('appearance')->label('Aspetto Esteriore Beni'),
                    Forms\Components\DateTimePicker::make('shipping_date')->label('Data Spedizione')->required()->default(now()),
                    Forms\Components\DateTimePicker::make('delivery_date')->label('Data Consegna'),
                ])->columns(2),
            Section::make('Articoli')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('product_id')->label('Prodotto')
                                ->relationship('product', 'name')->searchable()->preload()
                                ->live()
                                ->afterStateUpdated(function (Set $set, $state) {
                                    if ($state) {
                                        $product = \App\Models\Product::find($state);
                                        if ($product) {
                                            $set('description', $product->name);
                                            $set('weight', $product->weight);
                                        }
                                    }
                                }),
                            Forms\Components\TextInput::make('description')->label('Descrizione')->required()->columnSpan(2),
                            Forms\Components\TextInput::make('quantity')->label('Qtà')->numeric()->required()->default(1),
                            Forms\Components\TextInput::make('unit')->label('U.M.')->default('pz'),
                            Forms\Components\TextInput::make('weight')->label('Peso (g)')->numeric(),
                        ])
                        ->columns(6)
                        ->defaultItems(1)
                        ->addActionLabel('Aggiungi Articolo'),
                ]),
            Section::make('Note')
                ->schema([
                    Forms\Components\Textarea::make('notes')->label('Note'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('document_number')->label('N. DDT')->searchable()->sortable()->copyable(),
                Tables\Columns\TextColumn::make('recipient_name')->label('Destinatario')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('reason')->label('Causale')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (string $state) => match($state) {
                        'vendita' => 'Vendita', 'reso' => 'Reso',
                        'conto_lavorazione' => 'C/Lavorazione',
                        'omaggio' => 'Omaggio', default => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('status')->label('Stato')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'draft' => 'gray',
                        'ready' => 'warning',
                        'shipped' => 'info',
                        'delivered' => 'success',
                        default => 'gray'
                    })
                    ->formatStateUsing(fn (string $state) => match($state) {
                        'draft' => 'Bozza', 'ready' => 'Pronto',
                        'shipped' => 'Spedito', 'delivered' => 'Consegnato',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('shipping_date')->label('Data Spedizione')->dateTime('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('order.order_number')->label('Ordine')->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Stato')
                    ->options(['draft' => 'Bozza', 'ready' => 'Pronto', 'shipped' => 'Spedito', 'delivered' => 'Consegnato']),
                Tables\Filters\SelectFilter::make('reason')->label('Causale')
                    ->options([
                        'vendita' => 'Vendita', 'reso' => 'Reso',
                        'conto_lavorazione' => 'C/Lavorazione',
                        'omaggio' => 'Omaggio', 'riparazione' => 'Riparazione', 'altro' => 'Altro',
                    ]),
            ])
            ->recordActions([
                Action::make('download_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->action(function (TransportDocument $record) {
                        $record->load('items.product');
                        $pdf = Pdf::loadView('pdf.transport-document', ['document' => $record]);
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "ddt-{$record->document_number}.pdf"
                        );
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('shipping_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransportDocuments::route('/'),
            'create' => Pages\CreateTransportDocument::route('/create'),
            'edit' => Pages\EditTransportDocument::route('/{record}/edit'),
            'view' => Pages\ViewTransportDocument::route('/{record}'),
        ];
    }
}
