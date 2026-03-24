<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Exports\OrderExporter;
use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Models\Order;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static string|UnitEnum|null $navigationGroup = 'Vendite';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Ordine';
    
    protected static ?string $pluralModelLabel = 'Ordini';

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informazioni Ordine')
                ->icon('heroicon-o-shopping-bag')->iconColor('primary')
                ->schema([
                    TextEntry::make('order_number')
                        ->label('Numero Ordine')
                        ->size('lg')
                        ->weight('bold')
                        ->copyable(),

                    TextEntry::make('created_at')
                        ->label('Data Ordine')
                        ->dateTime('d/m/Y H:i'),

                    TextEntry::make('shipping_full_name')
                        ->label('Cliente')
                        ->weight('bold'),

                    TextEntry::make('customer_email')
                        ->label('Email')
                        ->copyable()
                        ->color('primary'),

                    TextEntry::make('status')
                        ->label('Stato Ordine')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'pending' => 'In Attesa',
                            'processing' => 'In Elaborazione',
                            'shipped' => 'Spedito',
                            'delivered' => 'Consegnato',
                            'cancelled' => 'Annullato',
                            default => $state,
                        })
                        ->color(fn (string $state): string => match ($state) {
                            'pending' => 'gray',
                            'processing' => 'warning',
                            'shipped' => 'info',
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                            default => 'gray',
                        }),

                    TextEntry::make('payment_status')
                        ->label('Stato Pagamento')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'pending' => 'In Attesa',
                            'paid' => 'Pagato',
                            'failed' => 'Fallito',
                            'refunded' => 'Rimborsato',
                            default => $state,
                        })
                        ->color(fn (string $state): string => match ($state) {
                            'pending' => 'warning',
                            'paid' => 'success',
                            'failed' => 'danger',
                            'refunded' => 'gray',
                            default => 'gray',
                        }),

                    TextEntry::make('payment_method')
                        ->label('Metodo Pagamento')
                        ->formatStateUsing(fn (?string $state): string => match ($state) {
                            'credit_card' => 'Carta di Credito',
                            'paypal' => 'PayPal',
                            'bank_transfer' => 'Bonifico Bancario',
                            default => $state ?? '—',
                        }),
                ])
                ->columns(3)->columnSpanFull(),

            Section::make('Indirizzo Spedizione')
                ->icon('heroicon-o-map-pin')->iconColor('info')
                ->schema([
                    TextEntry::make('shipping_full_name')->label('Destinatario'),
                    TextEntry::make('shipping_company')->label('Azienda')->placeholder('N/A'),
                    TextEntry::make('shipping_address')->label('Indirizzo'),
                    TextEntry::make('shipping_city')->label('Città'),
                    TextEntry::make('shipping_province')->label('Provincia'),
                    TextEntry::make('shipping_postal_code')->label('CAP'),
                    TextEntry::make('shipping_phone')->label('Telefono')->placeholder('N/A'),
                ])
                ->columns(3)->columnSpanFull(),

            Section::make('Articoli Ordinati')
                ->icon('heroicon-o-shopping-cart')->iconColor('primary')
                ->schema([
                    RepeatableEntry::make('items')
                        ->label('Prodotti')
                        ->schema([
                            TextEntry::make('full_product_name')->label('Prodotto')->weight('medium'),
                            TextEntry::make('product_sku')->label('SKU')->badge()->color('gray'),
                            TextEntry::make('price')->label('Prezzo')->money('EUR')->color('gray'),
                            TextEntry::make('quantity')->label('Qta'),
                            TextEntry::make('total')->label('Totale')->money('EUR')->weight('bold'),
                        ])
                        ->columns(5)->columnSpanFull(),
                ])->columns(3)->columnSpanFull(),

            Section::make('Riepilogo Importi')
                ->icon('heroicon-o-currency-euro')->iconColor('success')
                ->schema([
                    TextEntry::make('subtotal')->label('Subtotale')->money('EUR')->color('gray'),
                    TextEntry::make('discount_amount')
                        ->label('Sconto')
                        ->money('EUR')
                        ->color('danger')
                        ->visible(fn ($record) => $record->discount_amount > 0),
                    TextEntry::make('shipping_cost')->label('Spedizione')->money('EUR')->color('gray'),
                    TextEntry::make('tax_amount')->label('IVA')->money('EUR')->color('gray'),
                    TextEntry::make('total')
                        ->label('TOTALE')
                        ->money('EUR')
                        ->size('lg')
                        ->weight('bold')
                        ->color('success'),
                ])
                ->columns(5)->columnSpanFull(),

            Section::make('Note')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')->iconColor('gray')
                ->schema([
                    TextEntry::make('notes')->label('Note Cliente')->placeholder('Nessuna nota'),
                    TextEntry::make('admin_notes')->label('Note Admin')->placeholder('Nessuna nota'),
                ])
                ->columns(2)->columnSpanFull(),
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informazioni Ordine')
                    ->icon('heroicon-o-shopping-bag')->iconColor('primary')
                    ->aside()
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Numero Ordine')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('customer_email_display')
                            ->label('Cliente')
                            ->afterStateHydrated(fn ($component, $record) => $component->state($record?->customer_email ?? '—'))
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Select::make('status')
                            ->label('Stato Ordine')
                            ->options([
                                'pending' => 'In Attesa',
                                'processing' => 'In Elaborazione',
                                'shipped' => 'Spedito',
                                'delivered' => 'Consegnato',
                                'cancelled' => 'Annullato',
                            ])
                            ->disableOptionWhen(fn (string $value, Get $get): bool => match ($value) {
                                'shipped'   => $get('payment_status') !== 'paid',
                                'delivered' => true,
                                'cancelled' => true,
                                default     => false,
                            }),

                        Forms\Components\Select::make('payment_status')
                            ->label('Stato Pagamento')
                            ->options([
                                'pending' => 'In Attesa',
                                'paid' => 'Pagato',
                                'failed' => 'Fallito',
                                'refunded' => 'Rimborsato',
                            ])
                            ->disabled(),

                        Forms\Components\TextInput::make('payment_method_display')
                            ->label('Metodo Pagamento')
                            ->afterStateHydrated(fn ($component, $record) => $component->state(match ($record?->payment_method) {
                                'credit_card' => 'Carta di Credito',
                                'paypal' => 'PayPal',
                                'bank_transfer' => 'Bonifico Bancario',
                                default => $record?->payment_method ?? '—',
                            }))
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2)->columnSpanFull(),

                Section::make('Importi')
                    ->icon('heroicon-o-currency-euro')->iconColor('success')
                    ->aside()
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotale')
                            ->numeric()
                            ->prefix('€')
                            ->disabled(),

                        Forms\Components\TextInput::make('discount_amount')
                            ->label('Sconto')
                            ->numeric()
                            ->prefix('€')
                            ->disabled(),

                        Forms\Components\TextInput::make('shipping_cost')
                            ->label('Spedizione')
                            ->numeric()
                            ->prefix('€')
                            ->disabled(),

                        Forms\Components\TextInput::make('tax_amount')
                            ->label('IVA')
                            ->numeric()
                            ->prefix('€')
                            ->disabled(),

                        Forms\Components\TextInput::make('total')
                            ->label('Totale')
                            ->numeric()
                            ->prefix('€')
                            ->disabled(),
                    ])
                    ->columns(5)->columnSpanFull(),

                Section::make('Note')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')->iconColor('gray')
                    ->aside()
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Note Cliente')
                            ->rows(2)
                            ->disabled(),

                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Note Admin')
                            ->rows(2),
                    ])
                    ->columns(2)->columnSpanFull()
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('N. Ordine')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->tooltip('Clicca per copiare')
                    ->copyMessage('N. ordine copiato!')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('shipping_full_name')
                    ->label('Cliente')
                    ->searchable(['shipping_first_name', 'shipping_last_name'])
                    ->description(fn (Order $record): string => $record->customer_email ?? '')
                    ->sortable(['shipping_last_name', 'shipping_first_name'])
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Totale')
                    ->money('EUR')
                    ->sortable()
                    ->alignCenter()
                    ->weight('medium')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Stato Pagamento')
                    ->badge()
                    ->sortable()
                    ->alignCenter()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'In Attesa',
                        'paid' => 'Pagato',
                        'failed' => 'Fallito',
                        'refunded' => 'Rimborsato',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->sortable()
                    ->alignCenter()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'In Attesa',
                        'processing' => 'In Elaborazione',
                        'shipped' => 'Spedito',
                        'delivered' => 'Consegnato',
                        'cancelled' => 'Annullato',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'info',
                        'shipped' => 'warning',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Articoli')
                    ->counts('items')
                    ->toggleable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('id', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Stato Ordine')
                    ->placeholder('Tutti')
                    ->options([
                        'pending' => 'In Attesa',
                        'processing' => 'In Elaborazione',
                        'shipped' => 'Spedito',
                        'delivered' => 'Consegnato',
                        'cancelled' => 'Annullato',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Stato Pagamento')
                    ->placeholder('Tutti')
                    ->options([
                        'pending' => 'In Attesa',
                        'paid' => 'Pagato',
                        'failed' => 'Fallito',
                        'refunded' => 'Rimborsato',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label('Dal'),
                        DatePicker::make('created_until')
                            ->label('Al'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
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
                    ->modalHeading(fn (Order $record): string => "Ordine {$record->order_number}")
                    ->modalWidth('5xl'),
                ActionGroup::make([
                    EditAction::make()
                        ->label('Gestisci')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->color('info'),
                    RestoreAction::make()->label('Ripristina'),
                    DeleteAction::make()->label('Elimina'),
                    
                    Action::make('download_invoice')
                        ->label('Scarica Fattura')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('danger')
                        ->action(function (Order $record) {
                            $invoice = static::buildInvoiceData($record);
                            return response()->streamDownload(function () use ($invoice) {
                                echo Pdf::loadView('pdf.invoice', ['invoice' => $invoice])->output();
                            }, "FATTURA-{$record->order_number}.pdf");
                        }),
                    
                    Action::make('mark_as_shipped')
                        ->label('Segna come Spedito')
                        ->icon('heroicon-o-truck')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->visible(fn (Order $record) => $record->status === 'processing')
                        ->action(function (Order $record) {
                            if ($record->payment_status !== 'paid') {
                                \Filament\Notifications\Notification::make()
                                    ->title('Pagamento non ricevuto')
                                    ->body('Non è possibile spedire un ordine non ancora pagato.')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            $record->markAsShipped();
                            \Filament\Notifications\Notification::make()
                                ->title('Ordine segnato come spedito')
                                ->success()
                                ->send();
                        })
                        ->successNotification(null),
                    
                    Action::make('mark_as_delivered')
                        ->label('Segna come Consegnato')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (Order $record) => $record->status === 'shipped')
                        ->action(fn (Order $record) => $record->markAsDelivered())
                        ->successNotificationTitle('Ordine segnato come consegnato'),
                ]),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->label('Esporta Selezionati')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exporter(OrderExporter::class),
                BulkActionGroup::make([
                    RestoreBulkAction::make()->label('Ripristina selezionati'),
                    DeleteBulkAction::make()->label('Elimina selezionati'),
                ])->label('Azioni'),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Esporta Tutti')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exporter(OrderExporter::class),
            ])
            ->emptyStateHeading('Nessun ordine trovato')
            ->emptyStateDescription('Gli ordini dei clienti appariranno qui.')
            ->emptyStateIcon('heroicon-o-shopping-bag');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                \Illuminate\Database\Eloquent\SoftDeletingScope::class,
            ]);
    }

    public static function buildInvoiceData(Order $order): array
    {
        $order->loadMissing('items');

        $billingAddress = implode(', ', array_filter([
            $order->billing_address ?? $order->shipping_address,
            $order->billing_city ?? $order->shipping_city,
            ($order->billing_postal_code ?? $order->shipping_postal_code) . ' ' . ($order->billing_province ?? $order->shipping_province),
        ]));

        return [
            'invoice_number'  => $order->order_number,
            'status'          => $order->payment_status ?? 'pending',
            'issue_date'      => $order->created_at->format('d/m/Y'),
            'due_date'        => $order->created_at->format('d/m/Y'),
            'client_name'     => $order->billing_full_name ?? $order->shipping_full_name ?? '—',
            'client_address'  => $billingAddress ?: '—',
            'client_vat'      => $order->billing_vat_number ?? '—',
            'client_email'    => $order->customer_email ?? '—',
            'client_sdi_code' => null,
            'client_pec'      => null,
            'items'           => $order->items->map(fn ($item) => [
                'description' => $item->full_product_name,
                'quantity'    => $item->quantity,
                'unit_price'  => $item->price,
                'vat_rate'    => 22,
                'discount'    => 0,
                'total'       => $item->total,
            ])->toArray(),
            'subtotal'        => $order->subtotal ?? 0,
            'discount_amount' => $order->discount_amount ?? 0,
            'tax_amount'      => $order->tax_amount ?? 0,
            'total'           => $order->total ?? 0,
            'notes'           => $order->notes,
            'payment_method'  => match ($order->payment_method) {
                'credit_card'   => 'Carta di Credito',
                'paypal'        => 'PayPal',
                'bank_transfer' => 'Bonifico Bancario',
                default         => $order->payment_method ?? 'N/A',
            },
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() > 10 ? 'danger' : 'warning';
    }
}