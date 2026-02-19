<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransportDocumentItem extends Model
{
    protected $fillable = [
        'transport_document_id',
        'product_id',
        'product_variant_id',
        'description',
        'quantity',
        'unit',
        'weight',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
    ];

    public function transportDocument(): BelongsTo
    {
        return $this->belongsTo(TransportDocument::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
