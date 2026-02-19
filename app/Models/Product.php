<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'sku',
        'brand_id',
        'price',
        'compare_at_price',
        'cost',
        'stock_quantity',
        'low_stock_threshold',
        'vat_rate',
        'barcode',
        'weight',
        'dimensions',
        'is_active',
        'is_featured',
        'manage_stock',
        'main_image',
        'order',
        'views_count',
        'sales_count',
        'average_rating',
        'reviews_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'vat_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'manage_stock' => 'boolean',
        'order' => 'integer',
        'views_count' => 'integer',
        'sales_count' => 'integer',
        'average_rating' => 'decimal:2',
        'reviews_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
        
        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // Relazioni
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function warehouseMovements(): HasMany
    {
        return $this->hasMany(WarehouseMovement::class);
    }

    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class, 'promotion_product');
    }

    public function getMarginAttribute(): ?float
    {
        if (!$this->cost || $this->cost == 0) return null;
        return round((($this->price - $this->cost) / $this->price) * 100, 2);
    }

    public function getMarginAmountAttribute(): ?float
    {
        if (!$this->cost) return null;
        return round($this->price - $this->cost, 2);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('order');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class)->withTimestamps();
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function stockLogs(): HasMany
    {
        return $this->hasMany(StockLog::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where('manage_stock', false)
              ->orWhere('stock_quantity', '>', 0);
        });
    }

    public function scopeLowStock($query)
    {
        return $query->where('manage_stock', true)
                     ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                     ->where('stock_quantity', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('manage_stock', true)
                     ->where('stock_quantity', '<=', 0);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
    }

    public function scopeTopRated($query, float $minRating = 4.0)
    {
        return $query->where('average_rating', '>=', $minRating)
                     ->where('reviews_count', '>', 0)
                     ->orderBy('average_rating', 'desc');
    }

    public function scopeBestSellers($query, int $minSales = 10)
    {
        return $query->where('sales_count', '>=', $minSales)
                     ->orderBy('sales_count', 'desc');
    }

    // Helpers
    public function isInStock(): bool
    {
        if (!$this->manage_stock) {
            return true;
        }
        
        return $this->stock_quantity > 0;
    }

    public function isLowStock(): bool
    {
        if (!$this->manage_stock) {
            return false;
        }
        
        return $this->stock_quantity > 0 && $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function hasDiscount(): bool
    {
        return $this->compare_at_price && $this->compare_at_price > $this->price;
    }

    public function getDiscountPercentageAttribute(): ?float
    {
        if (!$this->hasDiscount()) {
            return null;
        }
        
        return round((($this->compare_at_price - $this->price) / $this->compare_at_price) * 100);
    }

    public function getPriceWithVatAttribute(): float
    {
        return $this->price * (1 + ($this->vat_rate / 100));
    }

    public function decreaseStock(int $quantity, string $reason = 'order', ?Order $order = null, ?User $user = null): void
    {
        if ($this->manage_stock) {
            $before = $this->stock_quantity;
            $this->decrement('stock_quantity', $quantity);
            $after = $this->fresh()->stock_quantity;
            
            // Log cambio stock
            StockLog::logChange($this, null, $before, $after, $reason, $order, $user);
        }
    }

    public function increaseStock(int $quantity, string $reason = 'adjustment', ?Order $order = null, ?User $user = null): void
    {
        if ($this->manage_stock) {
            $before = $this->stock_quantity;
            $this->increment('stock_quantity', $quantity);
            $after = $this->fresh()->stock_quantity;
            
            // Log cambio stock
            StockLog::logChange($this, null, $before, $after, $reason, $order, $user);
        }
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
        
        // Invalida cache se usi caching
        Cache::forget("product_{$this->id}_details");
    }

    public function incrementSales(int $quantity = 1): void
    {
        $this->increment('sales_count', $quantity);
    }

    public function updateRatings(): void
    {
        $approved = $this->reviews()->approved();
        
        $this->update([
            'average_rating' => $approved->avg('rating') ?? 0,
            'reviews_count' => $approved->count(),
        ]);
    }

    public function getApprovedReviewsCountAttribute(): int
    {
        return Cache::remember(
            "product_{$this->id}_reviews_count",
            3600,
            fn() => $this->reviews()->approved()->count()
        );
    }
}