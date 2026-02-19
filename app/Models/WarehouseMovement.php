<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseMovement extends Model
{
    protected $fillable = [
        'type',
        'reason',
        'product_id',
        'product_variant_id',
        'quantity',
        'stock_before',
        'stock_after',
        'unit_cost',
        'batch_number',
        'reference_number',
        'order_id',
        'supplier_order_id',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
    ];

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

    public function supplierOrder(): BelongsTo
    {
        return $this->belongsTo(SupplierOrder::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Registra un movimento di magazzino e aggiorna lo stock del prodotto/variante.
     */
    public static function register(array $data): static
    {
        $product = Product::findOrFail($data['product_id']);
        $variant = isset($data['product_variant_id']) ? ProductVariant::find($data['product_variant_id']) : null;

        $target = $variant ?? $product;
        $stockBefore = $target->stock_quantity;

        $quantity = abs($data['quantity']);
        if ($data['type'] === 'scarico') {
            $quantity = -$quantity;
        }

        $stockAfter = $stockBefore + $quantity;
        $target->update(['stock_quantity' => max(0, $stockAfter)]);

        return static::create([
            'type' => $data['type'],
            'reason' => $data['reason'],
            'product_id' => $data['product_id'],
            'product_variant_id' => $data['product_variant_id'] ?? null,
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => max(0, $stockAfter),
            'unit_cost' => $data['unit_cost'] ?? null,
            'batch_number' => $data['batch_number'] ?? null,
            'reference_number' => $data['reference_number'] ?? null,
            'order_id' => $data['order_id'] ?? null,
            'supplier_order_id' => $data['supplier_order_id'] ?? null,
            'user_id' => $data['user_id'] ?? auth()->id(),
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'carico' => 'Carico',
            'scarico' => 'Scarico',
            'reso' => 'Reso',
            default => $this->type,
        };
    }

    public function getReasonLabel(): string
    {
        return match ($this->reason) {
            'acquisto_fornitore' => 'Acquisto da Fornitore',
            'vendita_online' => 'Vendita Online',
            'vendita_negozio' => 'Vendita in Negozio',
            'reso_cliente' => 'Reso Cliente',
            'reso_fornitore' => 'Reso a Fornitore',
            'inventario' => 'Inventario',
            'aggiustamento' => 'Aggiustamento',
            'danneggiamento' => 'Danneggiamento',
            'omaggio' => 'Omaggio',
            'trasferimento' => 'Trasferimento',
            default => $this->reason,
        };
    }
}
