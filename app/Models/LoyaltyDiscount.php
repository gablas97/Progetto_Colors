<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyDiscount extends Model
{
    protected $fillable = [
        'name',
        'description',
        'tier',
        'discount_type',
        'discount_value',
        'points_required',
        'min_order_amount',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTier($query, string $tier)
    {
        return $query->where('tier', $tier);
    }

    public function getFormattedValueAttribute(): string
    {
        return $this->discount_type === 'percentage'
            ? "{$this->discount_value}%"
            : number_format($this->discount_value, 2) . ' €';
    }
}
