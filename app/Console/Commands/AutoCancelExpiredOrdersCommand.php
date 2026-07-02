<?php

namespace App\Console\Commands;

use App\Models\Deposit;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Finds and cancels orders/deposits with expired QRIS payments.
 * Scheduled to run every minute.
 */
class AutoCancelExpiredOrdersCommand extends Command
{
    protected $signature = 'orders:cancel-expired';

    protected $description = 'Cancel orders and deposits with expired QRIS payments';

    public function handle(OrderService $orderService): int
    {
        // Cancel expired orders
        $expiredOrders = Order::where('payment_status', 'pending')
            ->where('payment_method', 'qris')
            ->whereNotNull('qris_expired_at')
            ->where('qris_expired_at', '<', now())
            ->get();

        $cancelledOrders = 0;
        foreach ($expiredOrders as $order) {
            $orderService->cancelOrder($order);
            $cancelledOrders++;
        }

        // Expire pending deposits
        $expiredDeposits = Deposit::where('payment_status', 'pending')
            ->whereNotNull('qris_expired_at')
            ->where('qris_expired_at', '<', now())
            ->get();

        $cancelledDeposits = 0;
        foreach ($expiredDeposits as $deposit) {
            $deposit->update(['payment_status' => 'expired']);
            $cancelledDeposits++;
        }

        if ($cancelledOrders > 0 || $cancelledDeposits > 0) {
            Log::info('Auto-cancelled expired payments', [
                'orders' => $cancelledOrders,
                'deposits' => $cancelledDeposits,
            ]);
            $this->info("Cancelled {$cancelledOrders} orders, {$cancelledDeposits} deposits.");
        }

        return self::SUCCESS;
    }
}
