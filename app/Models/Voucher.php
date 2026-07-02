<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property string $type
 * @property float $value
 * @property float $min_purchase
 * @property int $max_usage
 * @property int $used_count
 * @property Carbon|null $expired_at
 */
class Voucher extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_purchase',
        'max_usage', 'used_count', 'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_purchase' => 'decimal:2',
            'max_usage' => 'integer',
            'used_count' => 'integer',
            'expired_at' => 'datetime',
        ];
    }

    /**
     * Check if voucher is still valid (not expired and under usage limit).
     */
    public function isValid(): bool
    {
        if ($this->expired_at && $this->expired_at->isPast()) {
            return false;
        }

        if ($this->max_usage > 0 && $this->used_count >= $this->max_usage) {
            return false;
        }

        return true;
    }

    /**
     * Scope: only valid vouchers (not expired, usage under limit).
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expired_at')
                ->orWhere('expired_at', '>', now());
        })->where(function ($q) {
            $q->where('max_usage', 0)
                ->orWhereColumn('used_count', '<', 'max_usage');
        });
    }
}
