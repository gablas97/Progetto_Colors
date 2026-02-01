<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'discount_code',
    ];

    // Relazioni
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeActive($query)
    {
        // Carrelli attivi = aggiornati nelle ultime 30 giorni
        return $query->where('updated_at', '>=', now()->subDays(30));
    }

    // Helpers
    public function getItemsCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
    }

    public function getTotalAttribute(): float
    {
        $subtotal = $this->subtotal;
        
        // Applica sconto se presente
        if ($this->discount_code) {
            $discount = Discount::active()->byCode($this->discount_code)->first();
            if ($discount && $discount->canApplyToOrder($subtotal)) {
                $subtotal -= $discount->calculateDiscount($subtotal);
            }
        }
        
        return $subtotal;
    }

    public function addItem(Product $product, ?ProductVariant $variant = null, int $quantity = 1): CartItem
    {
        // Cerca se esiste già
        $item = $this->items()
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variant?->id)
            ->first();

        if ($item) {
            // Aggiorna quantità
            $item->increment('quantity', $quantity);
            return $item->fresh();
        }

        // Crea nuovo item
        return $this->items()->create([
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'quantity' => $quantity,
        ]);
    }

    public function updateItemQuantity(CartItem $item, int $quantity): void
    {
        if ($quantity <= 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $quantity]);
        }
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    public function clear(): void
    {
        $this->items()->delete();
    }

    public function isEmpty(): bool
    {
        return $this->items()->count() === 0;
    }

    public function applyDiscount(string $code): bool
    {
        $discount = Discount::active()->byCode($code)->first();
        
        if (!$discount || !$discount->canApplyToOrder($this->subtotal)) {
            return false;
        }

        $this->update(['discount_code' => $code]);
        return true;
    }

    public function removeDiscount(): void
    {
        $this->update(['discount_code' => null]);
    }

    // Converti carrello guest in carrello utente dopo login
    public function convertToUser(User $user): void
    {
        // Controlla se utente ha già un carrello
        $userCart = self::forUser($user->id)->first();

        if ($userCart) {
            // Merge items
            foreach ($this->items as $item) {
                $userCart->addItem(
                    $item->product,
                    $item->productVariant,
                    $item->quantity
                );
            }
            
            // Elimina carrello guest
            $this->delete();
        } else {
            // Converti questo carrello
            $this->update([
                'user_id' => $user->id,
                'session_id' => null,
            ]);
        }
    }

    // Cleanup carrelli vecchi (da eseguire con scheduled job)
    public static function cleanupOldCarts(): void
    {
        self::where('updated_at', '<', now()->subDays(30))->delete();
    }
}