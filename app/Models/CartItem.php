<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variant_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    // IMPORTANTE: Eager load
    protected $with = ['product', 'productVariant'];

    // Relazioni
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    // Helpers
    public function getSubtotalAttribute(): float
    {
        if (!$this->product) {
            return 0;
        }
        
        return $this->product->price * $this->quantity;
    }

    public function isInStock(): bool
    {
        if ($this->product_variant_id) {
            return $this->productVariant 
                && $this->productVariant->stock_quantity >= $this->quantity;
        }

        if (!$this->product || !$this->product->manage_stock) {
            return true;
        }

        return $this->product->stock_quantity >= $this->quantity;
    }

    public function getMaxQuantityAttribute(): int
    {
        if ($this->product_variant_id) {
            return $this->productVariant?->stock_quantity ?? 0;
        }

        if (!$this->product || !$this->product->manage_stock) {
            return 999;
        }

        return $this->product->stock_quantity;
    }
}