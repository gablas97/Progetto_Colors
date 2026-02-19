<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quote_number',
        'user_id',
        'client_name',
        'client_email',
        'client_phone',
        'client_company',
        'client_vat_number',
        'client_address',
        'client_city',
        'client_province',
        'client_postal_code',
        'status',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total',
        'valid_until',
        'converted_order_id',
        'notes',
        'terms',
        'created_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'valid_until' => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($quote) {
            if (empty($quote->quote_number)) {
                $year = now()->format('Y');
                $last = static::withTrashed()->where('quote_number', 'like', "PREV-{$year}-%")->count();
                $quote->quote_number = sprintf("PREV-%s-%05d", $year, $last + 1);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function convertedOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'converted_order_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->items->sum('subtotal');
        $taxAmount = $this->items->sum('tax_amount');
        $total = $subtotal + $taxAmount - $this->discount_amount;

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
        ]);
    }

    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast();
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Bozza',
            'sent' => 'Inviato',
            'accepted' => 'Accettato',
            'rejected' => 'Rifiutato',
            'expired' => 'Scaduto',
            'converted' => 'Convertito in Ordine',
            default => $this->status,
        };
    }
}
