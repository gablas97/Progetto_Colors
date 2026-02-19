<?php


namespace App\Filament\Resources\Quotes;

use App\Models\Quote;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static string|UnitEnum|null $navigationGroup = 'Preventivi';
    protected static ?string $navigationLabel = 'Preventivi';
    protected static ?string $modelLabel = 'Preventivo';
    protected static ?string $pluralModelLabel = 'Preventivi';
    protected static ?int $navigationSort = 1;

    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dati Cliente')
                ->schema([
                    Forms\Components\Select::make('user_id')->label('Cliente Registrato')
                        ->relationship('user', 'email', fn ($query) => $query->where('role', 'customer'))
                        ->searchable()->preload()
                        ->live()
                        ->afterStateUpdated(function (Set $set, $state) {
                            if ($state) {
                                $user = \App\Models\User::find($state);
                                if ($user) {
                                    $set('client_name', $user->full_name);
                                    $set('client_email', $user->email);
                                    $set('client_phone', $user->phone);
                                }
                            }
                        }),
                    Forms\Components\TextInput::make('client_name')->label('Nome Cliente')->required(),
                    Forms\Components\TextInput::make('client_email')->label('Email')->email(),
                    Forms\Components\TextInput::make('client_phone')->label('Telefono'),
                    Forms\Components\TextInput::make('client_company')->label('Azienda'),
                    Forms\Components\TextInput::make('client_vat_number')->label('P.IVA'),
                    Forms\Components\TextInput::make('client_address')->label('Indirizzo'),
                    Forms\Components\TextInput::make('client_city')->label('Città'),
                    Forms\Components\TextInput::make('client_province')->label('Provincia')->maxLength(2),
                    Forms\Components\TextInput::make('client_postal_code')->label('CAP'),
                ])->columns(2),
            Section::make('Dettagli Preventivo')
                ->schema([
                    Forms\Components\TextInput::make('quote_number')->label('Numero Preventivo')
                        ->disabled()->dehydrated()->helperText('Generato automaticamente'),
                    Forms\Components\Select::make('status')->label('Stato')
                        ->options([
                            'draft' => 'Bozza',
                            'sent' => 'Inviato',
                            'accepted' => 'Accettato',
                            'rejected' => 'Rifiutato',
                            'expired' => 'Scaduto',
                            'converted' => 'Convertito in Ordine',
                        ])
                        ->default('draft'),
                    Forms\Components\DatePicker::make('valid_until')->label('Valido Fino Al'),
                ])->columns(3),
            Section::make('Voci Preventivo')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('product_id')->label('Prodotto')
                                ->relationship('product', 'name')
                                ->searchable()->preload()
                                ->live()
                                ->afterStateUpdated(function (Set $set, $state) {
                                    if ($state) {
                                        $product = \App\Models\Product::find($state);
                                        if ($product) {
                                            $set('description', $product->name);
                                            $set('unit_price', $product->price);
                                            $set('vat_rate', $product->vat_rate ?? 22);
                                        }
                                    }
                                }),
                            Forms\Components\TextInput::make('description')->label('Descrizione')->required()->columnSpan(2),
                            Forms\Components\TextInput::make('quantity')->label('Qtà')->numeric()->required()->default(1)->minValue(1),
                            Forms\Components\TextInput::make('unit_price')->label('Prezzo Unit.')->numeric()->required()->prefix('€'),
                            Forms\Components\TextInput::make('vat_rate')->label('IVA %')->numeric()->default(22),
                            Forms\Components\TextInput::make('discount_percentage')->label('Sconto %')->numeric()->default(0),
                            Forms\Components\Placeholder::make('line_total')
                                ->label('Totale Riga')
                                ->content(function (Get $get) {
                                    $qty = floatval($get('quantity') ?? 0);
                                    $price = floatval($get('unit_price') ?? 0);
                                    $discount = floatval($get('discount_percentage') ?? 0);
                                    $subtotal = $qty * $price * (1 - $discount / 100);
                                    return '€ ' . number_format($subtotal, 2);
                                }),
                        ])
                        ->columns(7)
                        ->defaultItems(1)
                        ->addActionLabel('Aggiungi Voce')
                        ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                            $subtotal = $data['quantity'] * $data['unit_price'] * (1 - ($data['discount_percentage'] ?? 0) / 100);
                            $data['subtotal'] = $subtotal;
                            $data['tax_amount'] = $subtotal * ($data['vat_rate'] / 100);
                            $data['total'] = $subtotal + $data['tax_amount'];
                            return $data;
                        })
                        ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                            $subtotal = $data['quantity'] * $data['unit_price'] * (1 - ($data['discount_percentage'] ?? 0) / 100);
                            $data['subtotal'] = $subtotal;
                            $data['tax_amount'] = $subtotal * ($data['vat_rate'] / 100);
                            $data['total'] = $subtotal + $data['tax_amount'];
                            return $data;
                        }),
                ]),
            Section::make('Importi')
                ->schema([
                    Forms\Components\TextInput::make('discount_amount')->label('Sconto Globale')->numeric()->prefix('€')->default(0),
                    Forms\Components\Textarea::make('notes')->label('Note'),
                    Forms\Components\Textarea::make('terms')->label('Termini e Condizioni'),
                ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('quote_number')->label('N. Preventivo')->searchable()->sortable()->copyable(),
                Tables\Columns\TextColumn::make('client_name')->label('Cliente')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Stato')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'draft' => 'gray',
                        'sent' => 'info',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        'expired' => 'warning',
                        'converted' => 'primary',
                        default => 'gray'
                    })
                    ->formatStateUsing(fn (string $state) => match($state) {
                        'draft' => 'Bozza', 'sent' => 'Inviato', 'accepted' => 'Accettato',
                        'rejected' => 'Rifiutato', 'expired' => 'Scaduto', 'converted' => 'Convertito',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('total')->label('Totale')->money('EUR')->sortable(),
                Tables\Columns\TextColumn::make('valid_until')->label('Valido Fino Al')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Creato il')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Stato')
                    ->options([
                        'draft' => 'Bozza', 'sent' => 'Inviato', 'accepted' => 'Accettato',
                        'rejected' => 'Rifiutato', 'expired' => 'Scaduto', 'converted' => 'Convertito',
                    ]),
            ])
            ->recordActions([
                Action::make('download_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->action(function (Quote $record) {
                        $record->load('items.product');
                        $pdf = Pdf::loadView('pdf.quote', ['quote' => $record]);
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "preventivo-{$record->quote_number}.pdf"
                        );
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
            'view' => Pages\ViewQuote::route('/{record}'),
        ];
    }
}
