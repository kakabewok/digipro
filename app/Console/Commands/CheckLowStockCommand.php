<?php

namespace App\Console\Commands;

use App\Models\WebsiteSetting;
use App\Services\StockService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Checks products with stock below threshold and logs alerts.
 * Scheduled to run every 30 minutes.
 */
class CheckLowStockCommand extends Command
{
    protected $signature = 'stock:check-low';

    protected $description = 'Check for products with low stock and log alerts';

    public function handle(StockService $stockService): int
    {
        $threshold = (int) WebsiteSetting::get('low_stock_threshold', 5);
        $lowStockProducts = $stockService->getLowStockProducts($threshold);

        if ($lowStockProducts->isEmpty()) {
            $this->info('All products have sufficient stock.');

            return self::SUCCESS;
        }

        foreach ($lowStockProducts as $product) {
            Log::warning('Low stock alert', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'available_stock' => $product->available_stock_count,
                'threshold' => $threshold,
            ]);

            $this->warn("⚠ {$product->name}: {$product->available_stock_count} items left");
        }

        $this->info("Found {$lowStockProducts->count()} products with low stock.");

        return self::SUCCESS;
    }
}
