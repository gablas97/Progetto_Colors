<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'document_number',
        'order_id',
        'user_id',
        'sender_name',
        'sender_address',
        'sender_city',
        'sender_province',
        'sender_postal_code',
        'recipient_name',
        'recipient_address',
        'recipient_city',
        'recipient_province',
        'recipient_postal_code',
        'recipient_country',
        'shipping_method',
        'carrier_name',
        'tracking_number',
        'packages_count',
        'total_weight',
        'appearance',
        'reason',
        'shipping_date',
        'delivery_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'total_weight' => 'decimal:2',
        'shipping_date' => 'datetime',
        'delivery_date' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($doc) {
            if (empty($doc->document_number)) {
                $year = now()->format('Y');
                $last = static::withTrashed()->where('document_number', 'like', "DDT-{$year}-%")->count();
                $doc->document_number = sprintf("DDT-%s-%05d", $year, $last + 1);
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
        return $this->hasMany(TransportDocumentItem::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Bozza',
            'ready' => 'Pronto',
            'shipped' => 'Spedito',
            'delivered' => 'Consegnato',
            default => $this->status,
        };
    }

    public function getReasonLabelAttribute(): string
    {
        return match ($this->reason) {
            'vendita' => 'Vendita',
            'reso' => 'Reso',
            'conto_lavorazione' => 'Conto Lavorazione',
            'omaggio' => 'Omaggio',
            'riparazione' => 'Riparazione',
            'altro' => 'Altro',
            default => $this->reason,
        };
    }
}
