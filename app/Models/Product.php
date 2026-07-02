<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $thumbnail
 * @property float $price_customer
 * @property float $price_reseller
 * @property float $price_bulk
 * @property int $min_bulk_qty
 * @property string $status
 */
class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'thumbnail',
        'price_customer', 'price_reseller', 'price_bulk', 'min_bulk_qty', 'status',
    ];

    protected function casts(): array
    {
        return [
            'price_customer' => 'decimal:2',
            'price_reseller' => 'decimal:2',
            'price_bulk' => 'decimal:2',
            'min_bulk_qty' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function (Product $product) {
            if ($product->isDirty('name') && ! $product->isDirty('slug')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Get the count of available stock items.
     */
    public function getAvailableStockCountAttribute(): int
    {
        return $this->stocks()->where('status', 'available')->count();
    }

    /**
     * Scope: only active products.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: products with low stock (below threshold).
     */
    public function scopeLowStock(Builder $query, int $threshold = 5): Builder
    {
        return $query->withCount(['stocks as available_stock_count' => function ($q) {
            $q->where('status', 'available');
        }])->having('available_stock_count', '<', $threshold);
    }
}
