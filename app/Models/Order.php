<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'guest_email',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_company',
        'shipping_address',
        'shipping_address_2',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
        'shipping_country',
        'shipping_phone',
        'billing_same_as_shipping',
        'billing_first_name',
        'billing_last_name',
        'billing_company',
        'billing_vat_number',
        'billing_tax_code',
        'billing_address',
        'billing_address_2',
        'billing_city',
        'billing_province',
        'billing_postal_code',
        'billing_country',
        'billing_phone',
        'subtotal',
        'discount_amount',
        'discount_code',
        'shipping_cost',
        'tax_amount',
        'total',
        'payment_method',
        'payment_status',
        'paid_at',
        'payment_transaction_id',
        'status',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'notes',
        'admin_notes',
        'source',
        'return_status',
        'return_reason',
        'return_requested_at',
        'return_completed_at',
        'billing_sdi_code',
        'billing_pec',
    ];

    protected $casts = [
        'billing_same_as_shipping' => 'boolean',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'return_requested_at' => 'datetime',
        'return_completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    // Relazioni
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function transportDocuments(): HasMany
    {
        return $this->hasMany(TransportDocument::class);
    }

    public function warehouseMovements(): HasMany
    {
        return $this->hasMany(WarehouseMovement::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['delivered', 'cancelled']);
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['delivered', 'cancelled']);
    }

    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeWithReturns($query)
    {
        return $query->where('return_status', '!=', 'none');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    // Helpers
    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $lastOrder = static::whereYear('created_at', $year)
                          ->orderBy('id', 'desc')
                          ->first();
        
        $number = $lastOrder ? ((int) substr($lastOrder->order_number, -5)) + 1 : 1;
        
        return 'ORD-' . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function getCustomerEmailAttribute(): string
    {
        return $this->user ? $this->user->email : $this->guest_email;
    }

    public function getShippingFullNameAttribute(): string
    {
        return $this->shipping_first_name . ' ' . $this->shipping_last_name;
    }

    public function getBillingFullNameAttribute(): string
    {
        if ($this->billing_same_as_shipping) {
            return $this->shipping_full_name;
        }
        
        return $this->billing_first_name . ' ' . $this->billing_last_name;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing']) && !$this->cancelled_at;
    }

    public function markAsPaid(?string $transactionId = null): void
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'payment_transaction_id' => $transactionId,
        ]);
    }

    public function markAsShipped(): void
    {
        $this->update([
            'status' => 'shipped',
            'shipped_at' => now(),
        ]);
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
        
        // Ripristina stock
        foreach ($this->items as $item) {
            if ($item->product_variant_id) {
                $item->productVariant?->increaseStock($item->quantity);
            } else {
                $item->product?->increaseStock($item->quantity);
            }
        }
    }
}