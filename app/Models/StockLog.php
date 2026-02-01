<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'quantity_before',
        'quantity_after',
        'change',
        'reason',
        'order_id',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
        'change' => 'integer',
    ];

    // Relazioni
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForVariant($query, int $variantId)
    {
        return $query->where('product_variant_id', $variantId);
    }

    public function scopeByReason($query, string $reason)
    {
        return $query->where('reason', $reason);
    }

    // Helper per creare log
    public static function logChange(
        ?Product $product = null,
        ?ProductVariant $variant = null,
        int $quantityBefore,
        int $quantityAfter,
        string $reason,
        ?Order $order = null,
        ?User $user = null,
        ?string $notes = null
    ): self {
        return self::create([
            'product_id' => $product?->id,
            'product_variant_id' => $variant?->id,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'change' => $quantityAfter - $quantityBefore,
            'reason' => $reason,
            'order_id' => $order?->id,
            'user_id' => $user?->id,
            'notes' => $notes,
        ]);
    }
}