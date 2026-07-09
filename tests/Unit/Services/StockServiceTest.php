<?php

namespace Tests\Unit\Services;

use App\Models\Stock;
use App\Services\StockService;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    private StockService $stockService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->stockService = app(StockService::class);
    }

    public function test_get_available_stock_count_returns_correct_count()
    {
        $product = $this->createProduct();
        $this->createStock($product, 3);
        
        $count = $this->stockService->getAvailableCount($product->id);
        
        $this->assertEquals(3, $count);
    }

    public function test_lock_and_pick_returns_one_available_stock()
    {
        $product = $this->createProduct();
        $this->createStock($product, 2);
        
        $stock = $this->stockService->lockAndPick($product->id);
        
        $this->assertNotNull($stock);
        $this->assertEquals('sold', $stock->status);
        $this->assertNotNull($stock->sold_at);
        $this->assertEquals($product->id, $stock->product_id);
    }

    public function test_lock_and_pick_returns_null_if_no_stock_available()
    {
        $product = $this->createProduct();
        
        $stock = $this->stockService->lockAndPick($product->id);
        
        $this->assertNull($stock);
    }

    public function test_release_sets_stock_status_back_to_available()
    {
        $product = $this->createProduct();
        $stock = $this->createStock($product, 1);
        $stock->update(['status' => 'sold', 'sold_at' => now()]);
        
        $result = $this->stockService->release($stock->id);
        
        $this->assertTrue($result);
        
        $stock->refresh();
        $this->assertEquals('available', $stock->status);
        $this->assertNull($stock->sold_at);
    }

    public function test_release_fails_if_stock_already_available()
    {
        $product = $this->createProduct();
        $stock = $this->createStock($product, 1); // Status is available
        
        $result = $this->stockService->release($stock->id);
        
        $this->assertFalse($result);
    }

    public function test_import_from_txt_creates_one_stock_record_per_line_and_skips_empty()
    {
        $product = $this->createProduct();
        
        $filePath = sys_get_temp_dir() . '/test_import.txt';
        $content = "VALUE1\nVALUE2\n  \nVALUE3\n";
        file_put_contents($filePath, $content);
        
        $count = $this->stockService->importFromFile($product->id, $filePath);
        
        $this->assertEquals(3, $count);
        $this->assertEquals(3, Stock::where('product_id', $product->id)->count());
        $this->assertDatabaseHas('stocks', ['value' => 'VALUE1']);
        $this->assertDatabaseHas('stocks', ['value' => 'VALUE2']);
        $this->assertDatabaseHas('stocks', ['value' => 'VALUE3']);
        
        unlink($filePath);
    }
}
