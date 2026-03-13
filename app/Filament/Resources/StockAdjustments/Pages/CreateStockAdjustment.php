<?php

namespace App\Filament\Resources\StockAdjustments\Pages;

use App\Filament\Resources\StockAdjustments\StockAdjustmentResource;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockLog;
use Filament\Resources\Pages\CreateRecord;

class CreateStockAdjustment extends CreateRecord
{
    protected static string $resource = StockAdjustmentResource::class;

    protected function handleRecordCreation(array $data): StockLog
    {
        $productId  = $data['product_id'];
        $variantId  = $data['product_variant_id'] ?? null;
        $type       = $data['type'];
        $quantity   = (int) $data['quantity'];
        $reason     = $data['reason'] ?? null;

        if ($variantId) {
            $variant = ProductVariant::findOrFail($variantId);
            if ($type === 'manual_load') {
                $variant->increaseStock($quantity, 'manual_load', $reason);
            } else {
                $variant->decreaseStock($quantity, 'manual_unload', $reason);
            }
        } else {
            $product = Product::findOrFail($productId);
            if ($type === 'manual_load') {
                $product->increaseStock($quantity, 'manual_load', $reason);
            } else {
                $product->decreaseStock($quantity, 'manual_unload', $reason);
            }
        }

        return StockLog::where('product_id', $productId)
            ->where('type', $type)
            ->latest()
            ->firstOrFail();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
