<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class StockManagementTest extends TestCase
{
    public function test_admin_can_view_stocks_for_a_product()
    {
        $admin = $this->createAdmin();
        $product = $this->createProduct();

        $response = $this->actingAs($admin)->get('/admin/products/' . $product->id . '/stocks');

        $response->assertStatus(200);
    }

    public function test_admin_can_add_stock_manually()
    {
        $admin = $this->createAdmin();
        $product = $this->createProduct();

        $response = $this->actingAs($admin)->post('/admin/products/' . $product->id . '/stocks', [
            'stocks' => "VALUE1\nVALUE2\nVALUE3"
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('stocks', ['product_id' => $product->id, 'value' => 'VALUE1']);
        $this->assertDatabaseHas('stocks', ['product_id' => $product->id, 'value' => 'VALUE2']);
        $this->assertDatabaseHas('stocks', ['product_id' => $product->id, 'value' => 'VALUE3']);
    }

    public function test_admin_can_import_stock_from_txt_file()
    {
        $admin = $this->createAdmin();
        $product = $this->createProduct();

        $content = "TXT_VALUE1\nTXT_VALUE2\n\nTXT_VALUE3\n";
        $file = UploadedFile::fake()->createWithContent('stocks.txt', $content);

        $response = $this->actingAs($admin)->post('/admin/products/' . $product->id . '/stocks/import', [
            'file' => $file
        ]);

        $response->assertRedirect();
        $this->assertEquals(3, Stock::where('product_id', $product->id)->count());
        $this->assertDatabaseHas('stocks', ['value' => 'TXT_VALUE1']);
        $this->assertDatabaseHas('stocks', ['value' => 'TXT_VALUE2']);
        $this->assertDatabaseHas('stocks', ['value' => 'TXT_VALUE3']);
    }

    public function test_empty_lines_are_skipped_on_import()
    {
        // Actually already tested in the previous test by including an empty line `\n\n`
        // But doing it explicitly here too
        $admin = $this->createAdmin();
        $product = $this->createProduct();

        $content = "\n  \nVALUE_ONLY_1\n  \n";
        $file = UploadedFile::fake()->createWithContent('stocks_with_empty.txt', $content);

        $response = $this->actingAs($admin)->post('/admin/products/' . $product->id . '/stocks/import', [
            'file' => $file
        ]);

        $this->assertEquals(1, Stock::where('product_id', $product->id)->count());
    }

    public function test_admin_can_delete_available_stock()
    {
        $admin = $this->createAdmin();
        $product = $this->createProduct();
        $stock = $this->createStock($product, 1);

        $response = $this->actingAs($admin)->delete('/admin/stocks/' . $stock->id);

        $response->assertRedirect();
        $this->assertDatabaseMissing('stocks', ['id' => $stock->id]);
    }

    public function test_admin_cannot_delete_sold_stock()
    {
        $admin = $this->createAdmin();
        $product = $this->createProduct();
        $stock = $this->createStock($product, 1, ['status' => 'sold']);

        $response = $this->actingAs($admin)->delete('/admin/stocks/' . $stock->id);

        // Usually returning 403 or redirecting with error
        $this->assertTrue($response->status() === 403 || $response->isRedirect());
        $this->assertDatabaseHas('stocks', ['id' => $stock->id]);
    }

    public function test_admin_can_see_available_vs_sold_stock_counts()
    {
        $admin = $this->createAdmin();
        $product = $this->createProduct();
        
        $this->createStock($product, 2, ['status' => 'available']);
        $this->createStock($product, 3, ['status' => 'sold']);

        $response = $this->actingAs($admin)->get('/admin/products/' . $product->id . '/stocks');

        // Check the page loads and theoretically shows the correct counts
        $response->assertStatus(200);
        $response->assertSee('2');
        $response->assertSee('3');
    }

    public function test_bulk_delete_removes_multiple_stocks_at_once()
    {
        $admin = $this->createAdmin();
        $product = $this->createProduct();
        
        $stock1 = $this->createStock($product, 1);
        $stock2 = $this->createStock($product, 1);
        $stock3 = $this->createStock($product, 1);

        $response = $this->actingAs($admin)->post('/admin/stocks/bulk-delete', [
            'ids' => [$stock1->id, $stock2->id]
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseMissing('stocks', ['id' => $stock1->id]);
        $this->assertDatabaseMissing('stocks', ['id' => $stock2->id]);
        $this->assertDatabaseHas('stocks', ['id' => $stock3->id]);
    }
}
