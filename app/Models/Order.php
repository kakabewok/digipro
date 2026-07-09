<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property int|null $stock_id
 * @property int|null $voucher_id
 * @property int $quantity
 * @property float $price
 * @property float $discount
 * @property float $total
 * @property string $payment_method
 * @property string $payment_status
 * @property string $status
 * @property string|null $qris_reference
 * @property string|null $qris_checkout_url
 * @property Carbon|null $qris_expired_at
 * @property string $invoice_number
 * @property string|null $notes
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'product_id', 'stock_id', 'voucher_id',
        'quantity', 'price', 'discount', 'total',
        'payment_method', 'payment_status', 'status',
        'qris_reference', 'qris_checkout_url', 'qris_expired_at',
        'invoice_number', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'quantity' => 'integer',
            'qris_expired_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    /**
     * Check if the order is completed and stock value can be shown.
     */
    public function canShowStockValue(): bool
    {
        return $this->status === 'completed' && $this->stock_id !== null;
    }
}
