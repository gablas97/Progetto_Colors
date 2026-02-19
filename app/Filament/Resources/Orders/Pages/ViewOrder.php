<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Modifica Ordine')
                ->color('gray'),
            
            Actions\Action::make('download_invoice')
                ->label('Scarica Fattura PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->action(function () {
                    return response()->streamDownload(function () {
                        echo \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', ['order' => $this->record])->output();
                    }, "fattura-{$this->record->order_number}.pdf");
                }),
            
            Actions\Action::make('mark_as_shipped')
                ->label('Segna come Spedito')
                ->icon('heroicon-o-truck')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'processing')
                ->action(fn () => $this->record->markAsShipped())
                ->successNotificationTitle('Ordine segnato come spedito'),
            
            Actions\Action::make('mark_as_delivered')
                ->label('Segna come Consegnato')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'shipped')
                ->action(fn () => $this->record->markAsDelivered())
                ->successNotificationTitle('Ordine segnato come consegnato'),
        ];
    }

    public function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->components([
                Section::make('Informazioni Ordine')
                    ->schema([
                        TextEntry::make('order_number')
                            ->label('Numero Ordine')
                            ->size('lg')
                            ->weight('bold')
                            ->copyable(),
                        
                        TextEntry::make('created_at')
                            ->label('Data Ordine')
                            ->dateTime('d/m/Y H:i'),
                        
                        TextEntry::make('customer_email')
                            ->label('Email Cliente')
                            ->copyable(),
                        
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
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'credit_card' => 'Carta di Credito',
                                'paypal' => 'PayPal',
                                'bank_transfer' => 'Bonifico Bancario',
                                default => $state,
                            }),
                    ])
                    ->columns(2),

                Section::make('Indirizzo Spedizione')
                    ->schema([
                        TextEntry::make('shipping_full_name')
                            ->label('Destinatario'),
                        
                        TextEntry::make('shipping_company')
                            ->label('Azienda')
                            ->placeholder('N/A'),
                        
                        TextEntry::make('shipping_address')
                            ->label('Indirizzo'),
                        
                        TextEntry::make('shipping_city')
                            ->label('Città'),
                        
                        TextEntry::make('shipping_province')
                            ->label('Provincia'),
                        
                        TextEntry::make('shipping_postal_code')
                            ->label('CAP'),
                        
                        TextEntry::make('shipping_phone')
                            ->label('Telefono')
                            ->placeholder('N/A'),
                    ])
                    ->columns(2),

                Section::make('Indirizzo Fatturazione')
                    ->schema([
                        TextEntry::make('billing_full_name')
                            ->label('Intestatario')
                            ->visible(fn ($record) => !$record->billing_same_as_shipping),
                        
                        TextEntry::make('billing_company')
                            ->label('Azienda')
                            ->placeholder('N/A')
                            ->visible(fn ($record) => !$record->billing_same_as_shipping),
                        
                        TextEntry::make('billing_vat_number')
                            ->label('P.IVA')
                            ->placeholder('N/A')
                            ->visible(fn ($record) => !$record->billing_same_as_shipping),
                        
                        TextEntry::make('billing_tax_code')
                            ->label('Codice Fiscale')
                            ->placeholder('N/A')
                            ->visible(fn ($record) => !$record->billing_same_as_shipping),
                        
                        TextEntry::make('billing_address')
                            ->label('Indirizzo')
                            ->visible(fn ($record) => !$record->billing_same_as_shipping),
                        
                        TextEntry::make('billing_same_as_shipping')
                            ->label('Indirizzo Fatturazione')
                            ->state('Uguale all\'indirizzo di spedizione')
                            ->visible(fn ($record) => $record->billing_same_as_shipping),
                    ])
                    ->columns(2)
                    ->collapsed(),

                Section::make('Articoli Ordinati')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                TextEntry::make('full_product_name')
                                    ->label('Prodotto'),
                                
                                TextEntry::make('product_sku')
                                    ->label('SKU'),
                                
                                TextEntry::make('price')
                                    ->label('Prezzo')
                                    ->money('EUR'),
                                
                                TextEntry::make('quantity')
                                    ->label('Quantità'),
                                
                                TextEntry::make('total')
                                    ->label('Totale')
                                    ->money('EUR'),
                            ])
                            ->columns(5),
                    ]),

                Section::make('Riepilogo Importi')
                    ->schema([
                        TextEntry::make('subtotal')
                            ->label('Subtotale')
                            ->money('EUR'),
                        
                        TextEntry::make('discount_amount')
                            ->label('Sconto')
                            ->money('EUR')
                            ->visible(fn ($record) => $record->discount_amount > 0),
                        
                        TextEntry::make('discount_code')
                            ->label('Codice Sconto')
                            ->badge()
                            ->color('success')
                            ->visible(fn ($record) => $record->discount_code),
                        
                        TextEntry::make('shipping_cost')
                            ->label('Spedizione')
                            ->money('EUR'),
                        
                        TextEntry::make('tax_amount')
                            ->label('IVA')
                            ->money('EUR'),
                        
                        TextEntry::make('total')
                            ->label('TOTALE')
                            ->money('EUR')
                            ->size('lg')
                            ->weight('bold')
                            ->color('success'),
                    ])
                    ->columns(2),

                Section::make('Note')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Note Cliente')
                            ->placeholder('Nessuna nota'),
                        
                        TextEntry::make('admin_notes')
                            ->label('Note Admin')
                            ->placeholder('Nessuna nota'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }
}