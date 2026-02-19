<?php


namespace App\Filament\Resources\Invoices;

use App\Models\Invoice;
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

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static string|UnitEnum|null $navigationGroup = 'Fatturazione';
    protected static ?string $navigationLabel = 'Fatture';
    protected static ?string $modelLabel = 'Fattura';
    protected static ?string $pluralModelLabel = 'Fatture';
    protected static ?int $navigationSort = 1;

    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dati Fattura')
                ->schema([
                    Forms\Components\TextInput::make('invoice_number')->label('Numero Fattura')
                        ->disabled()->dehydrated()->helperText('Generato automaticamente'),
                    Forms\Components\Select::make('type')->label('Tipo')
                        ->options(['invoice' => 'Fattura', 'credit_note' => 'Nota di Credito'])
                        ->default('invoice')->required(),
                    Forms\Components\Select::make('order_id')->label('Ordine Collegato')
                        ->relationship('order', 'order_number')
                        ->searchable()->preload(),
                    Forms\Components\Select::make('status')->label('Stato')
                        ->options([
                            'draft' => 'Bozza', 'sent' => 'Inviata', 'paid' => 'Pagata',
                            'overdue' => 'Scaduta', 'cancelled' => 'Annullata',
                        ])->default('draft'),
                ])->columns(2),
            Section::make('Dati Cliente')
                ->schema([
                    Forms\Components\Select::make('user_id')->label('Cliente')
                        ->relationship('user', 'email', fn ($query) => $query->where('role', 'customer'))
                        ->searchable()->preload()
                        ->live()
                        ->afterStateUpdated(function (Set $set, $state) {
                            if ($state) {
                                $user = \App\Models\User::find($state);
                                if ($user) {
                                    $set('client_name', $user->full_name);
                                    $set('client_email', $user->email);
                                }
                            }
                        }),
                    Forms\Components\TextInput::make('client_name')->label('Nome/Ragione Sociale')->required(),
                    Forms\Components\TextInput::make('client_email')->label('Email'),
                    Forms\Components\TextInput::make('client_company')->label('Azienda'),
                    Forms\Components\TextInput::make('client_vat_number')->label('P.IVA'),
                    Forms\Components\TextInput::make('client_tax_code')->label('Codice Fiscale'),
                    Forms\Components\TextInput::make('client_sdi_code')->label('Codice SDI')->maxLength(7),
                    Forms\Components\TextInput::make('client_pec')->label('PEC')->email(),
                    Forms\Components\TextInput::make('client_address')->label('Indirizzo'),
                    Forms\Components\TextInput::make('client_city')->label('Città'),
                    Forms\Components\TextInput::make('client_province')->label('Provincia')->maxLength(2),
                    Forms\Components\TextInput::make('client_postal_code')->label('CAP'),
                ])->columns(2),
            Section::make('Date e Pagamento')
                ->schema([
                    Forms\Components\DatePicker::make('issue_date')->label('Data Emissione')->required()->default(now()),
                    Forms\Components\DatePicker::make('due_date')->label('Data Scadenza'),
                    Forms\Components\DatePicker::make('paid_date')->label('Data Pagamento'),
                    Forms\Components\Select::make('payment_method')->label('Metodo Pagamento')
                        ->options([
                            'credit_card' => 'Carta di Credito',
                            'paypal' => 'PayPal',
                            'bank_transfer' => 'Bonifico Bancario',
                            'cash' => 'Contanti',
                            'other' => 'Altro',
                        ]),
                ])->columns(2),
            Section::make('Fatturazione Ricorrente')
                ->schema([
                    Forms\Components\Toggle::make('is_recurring')->label('Fattura Ricorrente')->live(),
                    Forms\Components\Select::make('recurring_interval')->label('Intervallo')
                        ->options(['monthly' => 'Mensile', 'quarterly' => 'Trimestrale', 'yearly' => 'Annuale'])
                        ->visible(fn (Get $get) => $get('is_recurring')),
                    Forms\Components\DatePicker::make('next_recurring_date')->label('Prossima Emissione')
                        ->visible(fn (Get $get) => $get('is_recurring')),
                ])->columns(3),
            Section::make('Voci Fattura')
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
                                            $set('unit_price', $product->price);
                                            $set('vat_rate', $product->vat_rate ?? 22);
                                        }
                                    }
                                }),
                            Forms\Components\TextInput::make('description')->label('Descrizione')->required()->columnSpan(2),
                            Forms\Components\TextInput::make('quantity')->label('Qtà')->numeric()->required()->default(1),
                            Forms\Components\TextInput::make('unit_price')->label('Prezzo Unit.')->numeric()->required()->prefix('€'),
                            Forms\Components\TextInput::make('vat_rate')->label('IVA %')->numeric()->default(22),
                            Forms\Components\TextInput::make('discount_percentage')->label('Sconto %')->numeric()->default(0),
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
            Section::make('Note')
                ->schema([
                    Forms\Components\TextInput::make('discount_amount')->label('Sconto Globale')->numeric()->prefix('€')->default(0),
                    Forms\Components\Textarea::make('notes')->label('Note'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')->label('N. Fattura')->searchable()->sortable()->copyable(),
                Tables\Columns\TextColumn::make('type')->label('Tipo')
                    ->badge()
                    ->color(fn($state) => $state === 'invoice' ? 'primary' : 'warning')
                    ->formatStateUsing(fn (string $state) => $state === 'invoice' ? 'Fattura' : 'Nota di Credito'),
                Tables\Columns\TextColumn::make('client_name')->label('Cliente')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Stato')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'draft' => 'gray',
                        'sent' => 'info',
                        'paid' => 'success',
                        'overdue' => 'danger',
                        'cancelled' => 'warning',
                        default => 'gray'
                    })
                    ->formatStateUsing(fn (string $state) => match($state) {
                        'draft' => 'Bozza', 'sent' => 'Inviata', 'paid' => 'Pagata',
                        'overdue' => 'Scaduta', 'cancelled' => 'Annullata', default => $state,
                    }),
                Tables\Columns\TextColumn::make('total')->label('Totale')->money('EUR')->sortable(),
                Tables\Columns\TextColumn::make('issue_date')->label('Emissione')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('due_date')->label('Scadenza')->date('d/m/Y')->sortable(),
                Tables\Columns\IconColumn::make('is_recurring')->label('Ricc.')->boolean()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Stato')
                    ->options([
                        'draft' => 'Bozza', 'sent' => 'Inviata', 'paid' => 'Pagata',
                        'overdue' => 'Scaduta', 'cancelled' => 'Annullata',
                    ]),
                Tables\Filters\SelectFilter::make('type')->label('Tipo')
                    ->options(['invoice' => 'Fattura', 'credit_note' => 'Nota di Credito']),
                Tables\Filters\TernaryFilter::make('is_recurring')->label('Ricorrenti'),
            ])
            ->recordActions([
                Action::make('download_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->action(function (Invoice $record) {
                        $record->load('items.product');
                        $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $record]);
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "fattura-{$record->invoice_number}.pdf"
                        );
                    }),
                Action::make('mark_paid')
                    ->label('Segna Pagata')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Invoice $record) => !in_array($record->status, ['paid', 'cancelled']))
                    ->action(fn (Invoice $record) => $record->markAsPaid()),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('issue_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'recurring' => Pages\RecurringInvoices::route('/recurring'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $overdue = Invoice::where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->count();
        return $overdue > 0 ? (string) $overdue : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
