<?php

namespace App\Filament\Exports;

use App\Models\Order;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class OrderExporter extends Exporter
{
    protected static ?string $model = Order::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('order_number')
                ->label('Numero Ordine'),
            
            ExportColumn::make('customer_email')
                ->label('Email Cliente'),
            
            ExportColumn::make('shipping_full_name')
                ->label('Nome Destinatario'),
            
            ExportColumn::make('shipping_address')
                ->label('Indirizzo'),
            
            ExportColumn::make('shipping_city')
                ->label('Città'),
            
            ExportColumn::make('shipping_postal_code')
                ->label('CAP'),
            
            ExportColumn::make('subtotal')
                ->label('Subtotale'),
            
            ExportColumn::make('discount_amount')
                ->label('Sconto'),
            
            ExportColumn::make('shipping_cost')
                ->label('Spedizione'),
            
            ExportColumn::make('tax_amount')
                ->label('IVA'),
            
            ExportColumn::make('total')
                ->label('Totale'),
            
            ExportColumn::make('payment_method')
                ->label('Metodo Pagamento'),
            
            ExportColumn::make('payment_status')
                ->label('Stato Pagamento'),
            
            ExportColumn::make('status')
                ->label('Stato Ordine'),
            
            ExportColumn::make('created_at')
                ->label('Data Creazione'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Esportazione ordini completata. ' . number_format($export->successful_rows) . ' ' . str('riga')->plural('righe') . ' esportate.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('riga')->plural('righe') . ' non esportate.';
        }

        return $body;
    }
}