<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $user_id
 * @property float $amount
 * @property string $payment_status
 * @property string|null $qris_reference
 * @property string|null $qris_checkout_url
 * @property Carbon|null $qris_expired_at
 */
class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'amount', 'payment_status',
        'qris_reference', 'qris_checkout_url', 'qris_expired_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'qris_expired_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
