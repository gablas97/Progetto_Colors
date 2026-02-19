<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Promotion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'value',
        'buy_quantity',
        'get_quantity',
        'min_order_amount',
        'usage_limit',
        'usage_count',
        'starts_at',
        'ends_at',
        'is_active',
        'banner_image',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($promo) {
            if (empty($promo->slug)) {
                $promo->slug = Str::slug($promo->name);
            }
        });
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'promotion_product');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'promotion_category');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('ends_at', '<', now());
    }

    public function isCurrentlyActive(): bool
    {
        return $this->is_active
            && $this->starts_at <= now()
            && $this->ends_at >= now()
            && ($this->usage_limit === null || $this->usage_count < $this->usage_limit);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'percentage' => 'Sconto %',
            'fixed' => 'Sconto Fisso',
            'buy_x_get_y' => 'Compra X Paga Y',
            'bundle' => 'Bundle',
            'shipping' => 'Spedizione Gratuita',
            default => $this->type,
        };
    }
}
