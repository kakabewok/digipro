<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

/**
 * Handles stock operations: locking, releasing, importing.
 */
class StockService
{
    /**
     * Lock and pick one available stock item for a product.
     * Uses pessimistic locking to prevent double allocation.
     *
     * @return Stock|null The locked stock item, or null if none available.
     */
    public function lockAndPick(int $productId): ?Stock
    {
        return DB::transaction(function () use ($productId) {
            $stock = Stock::where('product_id', $productId)
                ->where('status', 'available')
                ->lockForUpdate()
                ->first();

            if (! $stock) {
                return null;
            }

            $stock->update([
                'status' => 'sold',
                'sold_at' => now(),
            ]);

            return $stock;
        });
    }

    /**
     * Release a stock item back to available (e.g., on order cancellation).
     */
    public function release(int $stockId): bool
    {
        return DB::transaction(function () use ($stockId) {
            $stock = Stock::lockForUpdate()->find($stockId);

            if (! $stock || $stock->status !== 'sold') {
                return false;
            }

            $stock->update([
                'status' => 'available',
                'sold_at' => null,
            ]);

            return true;
        });
    }

    /**
     * Bulk add stock items from an array of values.
     *
     * @param  array<string>  $values  One value per stock item
     * @return int Number of items added
     */
    public function bulkAdd(int $productId, array $values): int
    {
        $records = [];
        $now = now();

        foreach ($values as $value) {
            $trimmed = trim($value);
            if ($trimmed === '') {
                continue;
            }

            $records[] = [
                'product_id' => $productId,
                'value' => $trimmed,
                'status' => 'available',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (empty($records)) {
            return 0;
        }

        // Insert in chunks of 500 for large imports
        foreach (array_chunk($records, 500) as $chunk) {
            Stock::insert($chunk);
        }

        return count($records);
    }

    /**
     * Import stock from a text file (one value per line).
     *
     * @return int Number of items imported
     */
    public function importFromFile(int $productId, string $filePath): int
    {
        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);

        return $this->bulkAdd($productId, $lines);
    }

    /**
     * Get available stock count for a product.
     */
    public function getAvailableCount(int $productId): int
    {
        return Stock::where('product_id', $productId)
            ->where('status', 'available')
            ->count();
    }

    /**
     * Get products with stock below threshold.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getLowStockProducts(int $threshold = 5)
    {
        return Product::withCount(['stocks as available_stock_count' => function ($q) {
            $q->where('status', 'available');
        }])
            ->having('available_stock_count', '<', $threshold)
            ->get();
    }
}
