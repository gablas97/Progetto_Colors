<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLog extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'reason',
        'reference_id',
        'reference_type',
        'user_id',
        'cancelled_at',
        'cancelled_by',
    ];

    protected $casts = [
        'quantity'       => 'integer',
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
        'cancelled_at'   => 'datetime',
    ];

    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null;
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForVariant($query, int $variantId)
    {
        return $query->where('product_variant_id', $variantId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'manual_load'             => 'Carico Manuale',
            'manual_unload'           => 'Scarico Manuale',
            'order_fulfilled'         => 'Ordine Spedito',
            'supplier_order_received' => 'Ordine Fornitore Ricevuto',
            'return'                  => 'Reso / Annullamento',
            default                   => $this->type,
        };
    }
}
