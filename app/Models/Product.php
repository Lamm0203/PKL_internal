<?php
// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'stock',
        'weight',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    // ================= RELATIONSHIPS =================

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function firstImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->oldestOfMany('sort_order');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // ================= ACCESSORS =================

    public function getDisplayPriceAttribute(): float
    {
        if ($this->discount_price !== null && $this->discount_price < $this->price) {
            return (float) $this->discount_price;
        }
        return (float) $this->price;
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->display_price, 0, ',', '.');
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_price !== null && $this->discount_price > 0 && $this->discount_price < $this->price;
    }

    public function getDiscountPercentageAttribute(): int
    {
        if (!$this->has_discount) return 0;

        $discount = $this->price - $this->discount_price;
        return (int) round(($discount / $this->price) * 100);
    }

    public function getImageUrlAttribute(): string
    {
        $image = $this->primaryImage ?? $this->firstImage ?? $this->images->first();

        return $image ? $image->image_url : asset('images/domba.png');
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->is_active && $this->stock > 0;
    }

    public function getStockLabelAttribute(): string
    {
        if ($this->stock <= 0) return 'Habis';
        if ($this->stock <= 5) return 'Sisa ' . $this->stock;
        return 'Tersedia';
    }

    public function getStockBadgeColorAttribute(): string
    {
        if ($this->stock <= 0) return 'danger';
        if ($this->stock <= 5) return 'warning';
        return 'success';
    }

    public function getFormattedWeightAttribute(): string
    {
        return $this->weight >= 1000 ? number_format($this->weight / 1000, 1) . ' kg' : $this->weight . ' gram';
    }

    // ================= SCOPES =================

    public function scopeActive($query) { return $query->where('is_active', true); }
    public function scopeFeatured($query) { return $query->where('is_featured', true); }
    public function scopeInStock($query) { return $query->where('stock', '>', 0); }
    public function scopeAvailable($query) { return $query->active()->inStock(); }
    public function scopeByCategory($query, string $slug) {
        return $query->whereHas('category', fn($q) => $q->where('slug', $slug));
    }
    public function scopeSortBy($query, ?string $sort)
    {
        return match ($sort) {
            'newest' => $query->latest(),
            'oldest' => $query->oldest(),
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'popular' => $query->withCount('orderItems')->orderByDesc('order_items_count'),
            default => $query->latest(),
        };
    }

    // ================= BOOT =================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $baseSlug = Str::slug($product->name);
                $slug = $baseSlug;
                $counter = 1;

                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }

                $product->slug = $slug;
            }
        });
    }

    // ================= HELPER METHODS =================

    public function decrementStock(int $quantity): bool
    {
        if ($this->stock < $quantity) return false;
        $this->decrement('stock', $quantity);
        return true;
    }

    public function incrementStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }

    public function hasStock(int $quantity = 1): bool
    {
        return $this->stock >= $quantity;
    }
}
