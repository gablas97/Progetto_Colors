<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    protected $fillable = [
        'quote_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'vat_rate',
        'discount_percentage',
        'subtotal',
        'tax_amount',
        'total',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public static function calculateFromInput(float $unitPrice, int $quantity, float $vatRate, float $discountPercentage = 0): array
    {
        $subtotal = $unitPrice * $quantity;
        if ($discountPercentage > 0) {
            $subtotal -= $subtotal * ($discountPercentage / 100);
        }
        $taxAmount = $subtotal * ($vatRate / 100);
        $total = $subtotal + $taxAmount;

        return compact('subtotal', 'tax_amount', 'total');
    }
}
