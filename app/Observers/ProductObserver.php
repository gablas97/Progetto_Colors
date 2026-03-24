<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\User;
use App\Filament\Resources\Inventory\InventoryResource;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Support\Str;

class ProductObserver
{
    public function saving(Product $product): void
    {
        if (empty($product->meta_title)) {
            $product->meta_title = Str::limit($product->name . ' | Colors S.r.l.', 60);
        }

        if (empty($product->meta_description)) {
            $raw = strip_tags($product->short_description ?? $product->description ?? '');
            $product->meta_description = Str::limit($raw, 160);
        }
    }

    public function updated(Product $product): void
    {
        // Notifica solo se stock_quantity è cambiato ed è sceso sotto la soglia
        if (
            ! $product->isDirty('stock_quantity') ||
            ! isset($product->low_stock_threshold) ||
            $product->low_stock_threshold <= 0
        ) {
            return;
        }

        $newStock = $product->stock_quantity;
        $oldStock = $product->getOriginal('stock_quantity');
        $threshold = $product->low_stock_threshold;

        // Solo se la soglia è stata appena superata (era sopra, ora sotto o uguale)
        if ($oldStock > $threshold && $newStock <= $threshold) {
            $admins = User::admins()->active()->get();

            $label = $newStock <= 0 ? 'Esaurito' : "Solo {$newStock} pz";

            foreach ($admins as $admin) {
                Notification::make()
                    ->title('Scorta bassa: ' . $product->name)
                    ->body("{$label} · soglia minima: {$threshold}")
                    ->icon('heroicon-o-exclamation-triangle')
                    ->iconColor($newStock <= 0 ? 'danger' : 'warning')
                    ->actions([
                        Action::make('view')
                            ->label('Gestisci scorte')
                            ->url(InventoryResource::getUrl('index'))
                            ->button(),
                    ])
                    ->tap(fn ($n) => $admin->notifyNow($n->toDatabase()));
            }
        }
    }
}
