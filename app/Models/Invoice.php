<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'type',
        'order_id',
        'user_id',
        'client_name',
        'client_email',
        'client_company',
        'client_vat_number',
        'client_tax_code',
        'client_sdi_code',
        'client_pec',
        'client_address',
        'client_city',
        'client_province',
        'client_postal_code',
        'client_country',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total',
        'status',
        'payment_method',
        'issue_date',
        'due_date',
        'paid_date',
        'is_recurring',
        'recurring_interval',
        'next_recurring_date',
        'parent_invoice_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'next_recurring_date' => 'date',
        'is_recurring' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $year = now()->format('Y');
                $prefix = $invoice->type === 'credit_note' ? 'NC' : 'FAT';
                $last = static::withTrashed()
                    ->where('invoice_number', 'like', "{$prefix}-{$year}-%")
                    ->count();
                $invoice->invoice_number = sprintf("{$prefix}-%s-%05d", $year, $last + 1);
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function parentInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'parent_invoice_id');
    }

    public function childInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'parent_invoice_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function markAsPaid(?string $paymentMethod = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => now(),
            'payment_method' => $paymentMethod ?? $this->payment_method,
        ]);
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'paid'
            && $this->due_date
            && $this->due_date->isPast();
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->items->sum('subtotal');
        $taxAmount = $this->items->sum('tax_amount');
        $total = $subtotal + $taxAmount - $this->discount_amount;

        $this->update(compact('subtotal', 'tax_amount', 'total'));
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'paid')
            ->where('due_date', '<', now());
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Bozza',
            'sent' => 'Inviata',
            'paid' => 'Pagata',
            'overdue' => 'Scaduta',
            'cancelled' => 'Annullata',
            default => $this->status,
        };
    }
}
