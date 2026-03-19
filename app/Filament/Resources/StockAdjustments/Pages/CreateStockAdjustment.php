<?php

namespace App\Filament\Resources\StockAdjustments\Pages;

use App\Filament\Resources\StockAdjustments\StockAdjustmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStockAdjustment extends CreateRecord
{
    protected static string $resource = StockAdjustmentResource::class;

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        // Aggiorna direttamente lo stock (senza creare un StockLog interno)
        // Il record StockLog viene creato da Filament tramite i $data restituiti.
        if ($data['product_variant_id']) {
            $variant = \App\Models\ProductVariant::find($data['product_variant_id']);
            $data['quantity_before'] = $variant->stock_quantity;

            if ($data['type'] === 'manual_load') {
                $variant->increment('stock_quantity', $data['quantity']);
            } else {
                $variant->decrement('stock_quantity', $data['quantity']);
            }

            $data['quantity_after'] = $variant->fresh()->stock_quantity;
        } else {
            $product = \App\Models\Product::find($data['product_id']);
            $data['quantity_before'] = $product->stock_quantity;

            if ($data['type'] === 'manual_load') {
                $product->increment('stock_quantity', $data['quantity']);
            } else {
                $product->decrement('stock_quantity', $data['quantity']);
            }

            $data['quantity_after'] = $product->fresh()->stock_quantity;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Movimento stock registrato con successo';
    }
}
