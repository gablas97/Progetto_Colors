<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'barcode',
        'stock_quantity',
        'image',
        'order',
        'is_active',
    ];

    protected $casts = [
        'stock_quantity' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relazioni
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stockLogs(): HasMany
    {
        return $this->hasMany(StockLog::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    // Helpers
    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function getFullNameAttribute(): string
    {
        return $this->product->name . ' - ' . $this->name;
    }

    public function decreaseStock(
        int $quantity,
        string $type = 'manual_unload',
        ?string $reason = null,
        ?int $referenceId = null,
        ?string $referenceType = null
    ): void {
        $before = $this->stock_quantity;
        $this->decrement('stock_quantity', $quantity);
        $after = $this->fresh()->stock_quantity;

        StockLog::create([
            'product_id'          => $this->product_id,
            'product_variant_id'  => $this->id,
            'type'                => $type,
            'quantity'            => $quantity,
            'quantity_before'     => $before,
            'quantity_after'      => $after,
            'reason'              => $reason,
            'reference_id'        => $referenceId,
            'reference_type'      => $referenceType,
            'user_id'             => auth()->id(),
        ]);
    }

    public function increaseStock(
        int $quantity,
        string $type = 'manual_load',
        ?string $reason = null,
        ?int $referenceId = null,
        ?string $referenceType = null
    ): void {
        $before = $this->stock_quantity;
        $this->increment('stock_quantity', $quantity);
        $after = $this->fresh()->stock_quantity;

        StockLog::create([
            'product_id'          => $this->product_id,
            'product_variant_id'  => $this->id,
            'type'                => $type,
            'quantity'            => $quantity,
            'quantity_before'     => $before,
            'quantity_after'      => $after,
            'reason'              => $reason,
            'reference_id'        => $referenceId,
            'reference_type'      => $referenceType,
            'user_id'             => auth()->id(),
        ]);
    }
}