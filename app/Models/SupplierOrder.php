<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'supplier_id',
        'status',
        'expected_delivery_date',
        'received_date',
        'subtotal',
        'tax_amount',
        'shipping_cost',
        'total',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'expected_delivery_date' => 'date',
        'received_date'          => 'date',
        'subtotal'               => 'decimal:2',
        'tax_amount'             => 'decimal:2',
        'shipping_cost'          => 'decimal:2',
        'total'                  => 'decimal:2',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $year = now()->format('Y');
                $last = static::withTrashed()->where('order_number', 'like', "OF-{$year}-%")->count();
                $order->order_number = sprintf("OF-%s-%05d", $year, $last + 1);
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SupplierOrderItem::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->items->sum('total_price');
        $taxAmount = $subtotal * 0.22;
        $this->update([
            'subtotal'   => $subtotal,
            'tax_amount' => $taxAmount,
            'total'      => $subtotal + $taxAmount + $this->shipping_cost,
        ]);
    }

    public function markAsReceived(): void
    {
        if ($this->status !== 'ordered' && $this->status !== 'confirmed') return;

        foreach ($this->items as $item) {
            if ($item->product_variant_id) {
                $item->productVariant?->increaseStock(
                    $item->quantity_ordered,
                    'supplier_order_received',
                    "Ordine fornitore {$this->order_number}",
                    $this->id,
                    'supplier_order'
                );
            } else {
                $item->product?->increaseStock(
                    $item->quantity_ordered,
                    'supplier_order_received',
                    "Ordine fornitore {$this->order_number}",
                    $this->id,
                    'supplier_order'
                );
            }
        }

        $this->update([
            'status'        => 'received',
            'received_date' => now()->toDateString(),
        ]);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'sent', 'confirmed']);
    }
}
