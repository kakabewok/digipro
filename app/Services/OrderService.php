<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Handles the full order lifecycle:
 * create, validate pricing, process payment, fulfill.
 */
class OrderService
{
    public function __construct(
        private InvoiceService $invoiceService,
        private StockService $stockService,
        private VoucherService $voucherService,
        private PaymentService $paymentService,
    ) {}

    /**
     * Determine the correct price for a user based on role permissions.
     */
    public function getPriceForUser(Product $product, User $user, int $quantity = 1): float
    {
        // Bulk price: user has bulk permission AND meets min qty
        if ($user->can('view bulk price') && $quantity >= $product->min_bulk_qty && $product->price_bulk > 0) {
            return (float) $product->price_bulk;
        }

        // Reseller price
        if ($user->can('view reseller price')) {
            return (float) $product->price_reseller;
        }

        // Default customer price
        return (float) $product->price_customer;
    }

    /**
     * Create an order with balance payment.
     * Validates balance, locks stock, deducts balance, and completes order in one transaction.
     *
     * @return array{success: bool, order: Order|null, error: string|null}
     */
    public function createWithBalance(User $user, Product $product, int $quantity = 1, ?string $voucherCode = null): array
    {
        return DB::transaction(function () use ($user, $product, $quantity, $voucherCode) {
            $price = $this->getPriceForUser($product, $user, $quantity);
            $subtotal = $price * $quantity;
            $discount = 0;
            $voucher = null;

            // Apply voucher if provided
            if ($voucherCode) {
                $voucherResult = $this->voucherService->validate($voucherCode, $subtotal);
                if (! $voucherResult['valid']) {
                    return ['success' => false, 'order' => null, 'error' => $voucherResult['error']];
                }
                $voucher = $voucherResult['voucher'];
                $discount = $this->voucherService->calculateDiscount($voucher, $subtotal);
            }

            $total = max(0, $subtotal - $discount);

            // Lock user row and check balance
            $lockedUser = User::lockForUpdate()->find($user->id);
            if ($lockedUser->balance < $total) {
                return ['success' => false, 'order' => null, 'error' => 'Insufficient balance.'];
            }

            // Check stock availability
            if ($this->stockService->getAvailableCount($product->id) < $quantity) {
                return ['success' => false, 'order' => null, 'error' => 'Product is out of stock.'];
            }

            // Lock and pick stock
            $stock = $this->stockService->lockAndPick($product->id);
            if (! $stock) {
                return ['success' => false, 'order' => null, 'error' => 'Failed to allocate stock.'];
            }

            // Deduct balance
            $lockedUser->decrement('balance', $total);

            // Increment voucher usage
            if ($voucher) {
                $this->voucherService->incrementUsage($voucher);
            }

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'stock_id' => $stock->id,
                'voucher_id' => $voucher?->id,
                'quantity' => $quantity,
                'price' => $price,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => 'balance',
                'payment_status' => 'paid',
                'status' => 'completed',
                'invoice_number' => $this->invoiceService->generate(),
            ]);

            return ['success' => true, 'order' => $order, 'error' => null];
        });
    }

    /**
     * Create an order with QRIS payment.
     * Generates QRIS, creates pending order.
     *
     * @return array{success: bool, order: Order|null, qr_url: string|null, checkout_url: string|null, error: string|null}
     */
    public function createWithQris(User $user, Product $product, int $quantity = 1, ?string $voucherCode = null): array
    {
        $price = $this->getPriceForUser($product, $user, $quantity);
        $subtotal = $price * $quantity;
        $discount = 0;
        $voucher = null;

        // Apply voucher if provided
        if ($voucherCode) {
            $voucherResult = $this->voucherService->validate($voucherCode, $subtotal);
            if (! $voucherResult['valid']) {
                return ['success' => false, 'order' => null, 'qr_url' => null, 'checkout_url' => null, 'error' => $voucherResult['error']];
            }
            $voucher = $voucherResult['voucher'];
            $discount = $this->voucherService->calculateDiscount($voucher, $subtotal);
        }

        $total = max(0, $subtotal - $discount);

        // Check stock availability
        if ($this->stockService->getAvailableCount($product->id) < $quantity) {
            return ['success' => false, 'order' => null, 'qr_url' => null, 'checkout_url' => null, 'error' => 'Product is out of stock.'];
        }

        // Generate QRIS via AutoGoPay
        $qris = $this->paymentService->generateQris((int) $total);
        if (! $qris['success']) {
            return ['success' => false, 'order' => null, 'qr_url' => null, 'checkout_url' => null, 'error' => $qris['error']];
        }

        // Create pending order
        $order = Order::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'voucher_id' => $voucher?->id,
            'quantity' => $quantity,
            'price' => $price,
            'discount' => $discount,
            'total' => $total,
            'payment_method' => 'qris',
            'payment_status' => 'pending',
            'status' => 'pending',
            'qris_reference' => $qris['transaction_id'],
            'qris_checkout_url' => $qris['checkout_url'],
            'qris_expired_at' => $qris['expiry_time'],
            'invoice_number' => $this->invoiceService->generate(),
        ]);

        // Increment voucher usage (will be reversed if order expires)
        if ($voucher) {
            $this->voucherService->incrementUsage($voucher);
        }

        return [
            'success' => true,
            'order' => $order,
            'qr_url' => $qris['qr_url'],
            'checkout_url' => $qris['checkout_url'],
            'error' => null,
        ];
    }

    /**
     * Process order after QRIS payment confirmed (called from webhook job).
     * Locks stock, marks as sold, completes order.
     */
    public function processAfterPayment(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            $lockedOrder = Order::lockForUpdate()->find($order->id);

            // Idempotency: skip if already processed
            if ($lockedOrder->status !== 'pending') {
                Log::info('Order already processed', ['order_id' => $order->id]);
                return true;
            }

            // Lock and pick stock
            $stock = $this->stockService->lockAndPick($lockedOrder->product_id);
            if (! $stock) {
                Log::error('Stock unavailable during order processing', ['order_id' => $order->id]);
                $lockedOrder->update([
                    'status' => 'cancelled',
                    'notes' => 'Stock unavailable after payment',
                ]);

                return false;
            }

            $lockedOrder->update([
                'stock_id' => $stock->id,
                'payment_status' => 'paid',
                'status' => 'completed',
            ]);

            return true;
        });
    }

    /**
     * Cancel an expired order and release stock if locked.
     */
    public function cancelOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->update([
                'payment_status' => 'expired',
                'status' => 'cancelled',
            ]);

            // Release stock if it was locked
            if ($order->stock_id) {
                $this->stockService->release($order->stock_id);
            }

            // Reverse voucher usage if applicable
            if ($order->voucher_id) {
                $voucher = Voucher::find($order->voucher_id);
                if ($voucher && $voucher->used_count > 0) {
                    $voucher->decrement('used_count');
                }
            }
        });
    }
}
