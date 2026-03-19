<?php

namespace App\Filament\Resources\Invoices;

use App\Models\Invoice;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
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

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
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
                            'draft'     => 'Bozza',
                            'sent'      => 'Inviata',
                            'paid'      => 'Pagata',
                            'overdue'   => 'Scaduta',
                            'cancelled' => 'Annullata',
                        ])->default('draft'),
                ])->columns(2),

            Section::make('Dati Cliente')
                ->schema([
                    Forms\Components\Select::make('user_id')->label('Cliente')
                        ->relationship('user', 'email')
                        ->searchable()->preload()
                        ->live()
                        ->afterStateUpdated(function (Set $set, $state) {
                            if ($state) {
                                $user = \App\Models\User::find($state);
                                if ($user) {
                                    $set('client_name', $user->name);
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
                            'credit_card'   => 'Carta di Credito',
                            'paypal'        => 'PayPal',
                            'bank_transfer' => 'Bonifico Bancario',
                            'cash'          => 'Contanti',
                            'other'         => 'Altro',
                        ]),
                ])->columns(2),

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
                            $data['subtotal']   = $subtotal;
                            $data['tax_amount'] = $subtotal * ($data['vat_rate'] / 100);
                            $data['total']      = $subtotal + $data['tax_amount'];
                            return $data;
                        })
                        ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                            $subtotal = $data['quantity'] * $data['unit_price'] * (1 - ($data['discount_percentage'] ?? 0) / 100);
                            $data['subtotal']   = $subtotal;
                            $data['tax_amount'] = $subtotal * ($data['vat_rate'] / 100);
                            $data['total']      = $subtotal + $data['tax_amount'];
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

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Dati Fattura')
                ->schema([
                    TextEntry::make('invoice_number')
                        ->label('Numero Fattura')
                        ->copyable()
                        ->weight('bold'),
                    TextEntry::make('type')
                        ->label('Tipo')
                        ->formatStateUsing(fn ($state) => $state === 'invoice' ? 'Fattura' : 'Nota di Credito')
                        ->badge()
                        ->color(fn ($state) => $state === 'invoice' ? 'primary' : 'warning'),
                    TextEntry::make('status')
                        ->label('Stato')
                        ->badge()
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'draft'     => 'Bozza',
                            'sent'      => 'Inviata',
                            'paid'      => 'Pagata',
                            'overdue'   => 'Scaduta',
                            'cancelled' => 'Annullata',
                            default     => $state,
                        })
                        ->color(fn ($state) => match ($state) {
                            'paid'      => 'success',
                            'sent'      => 'info',
                            'overdue'   => 'danger',
                            'cancelled' => 'warning',
                            default     => 'gray',
                        }),
                    TextEntry::make('order.order_number')
                        ->label('Ordine Collegato')
                        ->placeholder('—'),
                ])->columns(2),

            Section::make('Dati Cliente')
                ->schema([
                    TextEntry::make('client_name')->label('Nome/Ragione Sociale'),
                    TextEntry::make('client_email')->label('Email')->placeholder('—'),
                    TextEntry::make('client_company')->label('Azienda')->placeholder('—'),
                    TextEntry::make('client_vat_number')->label('P.IVA')->placeholder('—'),
                    TextEntry::make('client_tax_code')->label('Codice Fiscale')->placeholder('—'),
                    TextEntry::make('client_sdi_code')->label('Codice SDI')->placeholder('—'),
                    TextEntry::make('client_pec')->label('PEC')->placeholder('—'),
                    TextEntry::make('client_address')
                        ->label('Indirizzo')
                        ->formatStateUsing(fn ($state, Invoice $record): string => implode(', ', array_filter([
                            $record->client_address,
                            $record->client_postal_code . ' ' . $record->client_city,
                            $record->client_province,
                        ])) ?: '—')
                        ->columnSpanFull(),
                ])->columns(2)->collapsible(),

            Section::make('Voci Fattura')
                ->schema([
                    RepeatableEntry::make('items')
                        ->label('Prodotti')
                        ->schema([
                            TextEntry::make('description')->label('Descrizione')->columnSpan(2),
                            TextEntry::make('quantity')->label('Qtà'),
                            TextEntry::make('unit_price')->label('Prezzo Unit.')->money('EUR'),
                            TextEntry::make('vat_rate')->label('IVA')->suffix('%'),
                            TextEntry::make('discount_percentage')->label('Sconto')->suffix('%'),
                            TextEntry::make('total')->label('Totale')->money('EUR')->weight('bold')->columnSpanFull(),
                        ])
                        ->columns(6)
                        ->columnSpanFull(),
                ]),

            Section::make('Importi e Date')
                ->schema([
                    TextEntry::make('subtotal')->label('Subtotale')->money('EUR'),
                    TextEntry::make('discount_amount')->label('Sconto Globale')->money('EUR'),
                    TextEntry::make('tax_amount')->label('IVA')->money('EUR'),
                    TextEntry::make('total')->label('Totale')->money('EUR')->weight('bold')->size('lg'),
                    TextEntry::make('issue_date')->label('Data Emissione')->date('d/m/Y'),
                    TextEntry::make('due_date')->label('Data Scadenza')->date('d/m/Y')->placeholder('—'),
                    TextEntry::make('paid_date')->label('Data Pagamento')->date('d/m/Y')->placeholder('—'),
                    TextEntry::make('payment_method')
                        ->label('Metodo Pagamento')
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'credit_card'   => 'Carta di Credito',
                            'paypal'        => 'PayPal',
                            'bank_transfer' => 'Bonifico Bancario',
                            'cash'          => 'Contanti',
                            'other'         => 'Altro',
                            default         => '—',
                        }),
                ])->columns(4),

            Section::make('Note')
                ->schema([
                    TextEntry::make('notes')->label('Nota')->placeholder('Nessuna nota')->columnSpanFull(),
                ])->collapsible()->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('N. Fattura')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable()
                    ->tooltip('Clicca per copiare'),
                    
                TextColumn::make('type')->label('Tipo')
                    ->badge()
                    ->color(fn ($state) => $state === 'invoice' ? 'primary' : 'warning')
                    ->formatStateUsing(fn (string $state) => $state === 'invoice' ? 'Fattura' : 'Nota di Credito')
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('client_name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')->label('Stato')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'draft'     => 'gray',
                        'sent'      => 'info',
                        'paid'      => 'success',
                        'overdue'   => 'danger',
                        'cancelled' => 'warning',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'draft'     => 'Bozza',
                        'sent'      => 'Inviata',
                        'paid'      => 'Pagata',
                        'overdue'   => 'Scaduta',
                        'cancelled' => 'Annullata',
                        default     => $state,
                    })
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('total')
                    ->label('Totale')
                    ->money('EUR')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('issue_date')
                    ->label('Emissione')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('due_date')
                    ->label('Scadenza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->defaultSort('issue_date', 'desc')
            ->recordUrl(null)
            ->recordAction(ViewAction::class)
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Stato')
                    ->options([
                        'draft'     => 'Bozza',
                        'sent'      => 'Inviata',
                        'paid'      => 'Pagata',
                        'overdue'   => 'Scaduta',
                        'cancelled' => 'Annullata',
                    ]),
                Tables\Filters\SelectFilter::make('type')->label('Tipo')
                    ->options(['invoice' => 'Fattura', 'credit_note' => 'Nota di Credito']),
            ])
            ->recordActions([
                ViewAction::make()
                    ->extraAttributes(['style' => 'display:none'])
                    ->modalHeading(fn (Invoice $record): string => "Fattura {$record->invoice_number}")
                    ->modalWidth('5xl'),
                ActionGroup::make([
                    Action::make('download_pdf')
                        ->label('Scarica PDF')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->action(function (Invoice $record) {
                            $record->load('items.product');
                            $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $record]);
                            return response()->streamDownload(
                                fn () => print($pdf->output()),
                                "FATTURA-{$record->invoice_number}.pdf"
                            );
                        }),
                    Action::make('mark_paid')
                        ->label('Segna Pagata')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (Invoice $record) => !in_array($record->status, ['paid', 'cancelled']))
                        ->modalHeading('Segna come Pagata')
                        ->modalDescription('La fattura verrà segnata come "Pagata". Continuare?')
                        ->action(fn (Invoice $record) => $record->markAsPaid())
                        ->successNotificationTitle('Fattura segnata come pagata'),
                    EditAction::make()
                        ->label('Modifica')
                        ->color('warning'),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_download_pdfs')
                        ->label('Scarica PDF selezionati')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) {
                            $tempFile = tempnam(sys_get_temp_dir(), 'fatture_');
                            $zip = new \ZipArchive();
                            $zip->open($tempFile, \ZipArchive::OVERWRITE);

                            foreach ($records as $record) {
                                $record->load('items.product');
                                $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $record]);
                                $zip->addFromString("FATTURA-{$record->invoice_number}.pdf", $pdf->output());
                            }

                            $zip->close();

                            return response()->streamDownload(function () use ($tempFile) {
                                echo file_get_contents($tempFile);
                                unlink($tempFile);
                            }, 'FATTURE-' . now()->format('Y-m-d') . '.zip');
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('bulk_mark_paid')
                        ->label('Segna come pagate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Segna come pagate')
                        ->modalDescription('Le fatture selezionate verranno segnate come "Pagate". Continuare?')
                        ->action(fn (Collection $records) => $records->each(fn (Invoice $record) => $record->markAsPaid()))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit'   => Pages\EditInvoice::route('/{record}/edit'),
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
