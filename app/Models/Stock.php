<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $product_id
 * @property string $value
 * @property string $status
 * @property Carbon|null $sold_at
 */
class Stock extends Model
{
    protected $fillable = ['product_id', 'value', 'status', 'sold_at'];

    protected function casts(): array
    {
        return [
            'sold_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope: only available stock items.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope: only sold stock items.
     */
    public function scopeSold(Builder $query): Builder
    {
        return $query->where('status', 'sold');
    }
}
