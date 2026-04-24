<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Category;
use App\Models\Brand;

class Discount extends Model
{
    use HasFactory;

    protected static function boot(): void
    {
        parent::boot();
        static::saving(function ($discount) {
            if ($discount->code) {
                $discount->code = strtoupper($discount->code);
            }
        });
    }

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'min_order_amount',
        'usage_limit',
        'is_single_use_per_user',
        'usage_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'is_single_use_per_user' => 'boolean',
    ];

    // Relazioni
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withTimestamps();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'discount_category')->withTimestamps();
    }

    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'discount_brand')->withTimestamps();
    }

    public function usages(): HasMany
    {
        return $this->hasMany(DiscountUsage::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function ($q) {
                         $q->whereNull('starts_at')
                           ->orWhere('starts_at', '<=', now());
                     })
                     ->where(function ($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>=', now());
                     });
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', strtoupper($code));
    }

    // Helpers
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && $now->gt($this->expires_at)) {
            return false;
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function hasBeenUsedBy(int $userId): bool
    {
        return $this->usages()->where('user_id', $userId)->exists();
    }

    public function canApplyToOrder(float $orderAmount, ?int $userId = null): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->min_order_amount && $orderAmount < $this->min_order_amount) {
            return false;
        }

        if ($this->is_single_use_per_user && $userId !== null && $this->hasBeenUsedBy($userId)) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if ($this->type === 'percentage') {
            return ($amount * $this->value) / 100;
        }

        if ($this->type === 'fixed') {
            return min($this->value, $amount);
        }

        if ($this->type === 'shipping') {
            return $amount; // Azzera le spese di spedizione
        }

        return 0;
    }

    public function incrementUsage(?int $userId = null, ?int $orderId = null): void
    {
        $this->increment('usage_count');
        $this->refresh();

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            $this->update(['is_active' => false]);
        }

        if ($userId !== null) {
            $this->usages()->updateOrCreate(
                ['user_id' => $userId],
                ['order_id' => $orderId, 'used_at' => now()],
            );
        }
    }
}