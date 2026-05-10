<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Product model representing a catalog item.
 * Uses many-to-many relationship with Branch via branch_product pivot
 * for multi-branch stock management.
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'sku',
        'image_path',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)
            ->orderBy('sort_order')
            ->latestOfMany();
    }

    /**
     * Many-to-Many relationship with Branch.
     * Includes the stock_level from the pivot table.
     */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class)
                    ->withPivot('stock_level', 'low_stock_threshold')
                    ->withTimestamps();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the current price, considering active flash sales.
     */
    public function getCurrentPriceAttribute()
    {
        $flashSale = $this->activeFlashSale();
        return $flashSale ? $flashSale->discount_price : $this->price;
    }

    /**
     * Check if the product is currently on flash sale.
     */
    public function getOnFlashSaleAttribute(): bool
    {
        return $this->activeFlashSale() !== null;
    }

    /**
     * Users who have wishlisted this product.
     */
    public function wishlistedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlists')
                    ->withTimestamps();
    }

    /**
     * Order items containing this product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Active flash sales for this product.
     */
    public function flashSales(): HasMany
    {
        return $this->hasMany(FlashSale::class);
    }

    /**
     * Get the active flash sale for this product, if any.
     */
    public function activeFlashSale()
    {
        $now = now();
        return $this->flashSales
            ->filter(function ($sale) use ($now) {
                return $sale->is_active 
                    && $sale->starts_at <= $now 
                    && $sale->ends_at >= $now;
            })
            ->first();
    }
}