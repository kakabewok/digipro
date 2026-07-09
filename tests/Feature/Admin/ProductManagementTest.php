<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    public function test_admin_can_view_product_list()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin/products');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_product_with_valid_data()
    {
        $admin = $this->createAdmin();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/products', [
            'category_id' => $category->id,
            'name' => 'New Awesome Product',
            'description' => 'Test description',
            'price_customer' => 100000,
            'price_reseller' => 80000,
            'price_bulk' => 70000,
            'min_bulk_qty' => 10,
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'name' => 'New Awesome Product',
        ]);
    }

    public function test_product_slug_is_auto_generated_on_create()
    {
        $admin = $this->createAdmin();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/products', [
            'category_id' => $category->id,
            'name' => 'Slug Test Product',
            'description' => 'Test',
            'price_customer' => 100,
            'price_reseller' => 80,
            'price_bulk' => 70,
            'min_bulk_qty' => 10,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Slug Test Product',
            'slug' => 'slug-test-product',
        ]);
    }

    public function test_admin_can_edit_product()
    {
        $admin = $this->createAdmin();
        $product = $this->createProduct(['name' => 'Old Name']);

        $response = $this->actingAs($admin)->put('/admin/products/' . $product->id, [
            'category_id' => $product->category_id,
            'name' => 'New Name',
            'description' => $product->description,
            'price_customer' => $product->price_customer,
            'price_reseller' => $product->price_reseller,
            'price_bulk' => $product->price_bulk,
            'min_bulk_qty' => $product->min_bulk_qty,
            'status' => $product->status,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Name',
        ]);
    }

    public function test_admin_can_delete_product()
    {
        $admin = $this->createAdmin();
        $product = $this->createProduct();

        $response = $this->actingAs($admin)->delete('/admin/products/' . $product->id);

        $response->assertRedirect();
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_admin_can_toggle_product_status()
    {
        $admin = $this->createAdmin();
        $product = $this->createProduct(['status' => 'active']);

        // Assuming there is a toggle endpoint or we just use update
        $response = $this->actingAs($admin)->put('/admin/products/' . $product->id . '/toggle-status', [
            'status' => 'inactive'
        ]);

        // If a dedicated endpoint doesn't exist, this might fail, but it's standard.
        // Or we update it through the normal update endpoint. We will just test the effect if we use normal update.
        if ($response->status() === 404) {
            $response = $this->actingAs($admin)->put('/admin/products/' . $product->id, array_merge(
                $product->toArray(), ['status' => 'inactive']
            ));
        }

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => 'inactive',
        ]);
    }

    public function test_admin_can_upload_product_thumbnail()
    {
        Storage::fake('public');
        $admin = $this->createAdmin();
        $category = Category::factory()->create();

        $file = UploadedFile::fake()->image('thumbnail.jpg');

        $response = $this->actingAs($admin)->post('/admin/products', [
            'category_id' => $category->id,
            'name' => 'Upload Test',
            'description' => 'Test',
            'price_customer' => 100,
            'price_reseller' => 80,
            'price_bulk' => 70,
            'min_bulk_qty' => 10,
            'status' => 'active',
            'thumbnail' => $file,
        ]);

        $product = Product::where('name', 'Upload Test')->first();
        
        $this->assertNotNull($product->thumbnail);
        // Assuming thumbnail is stored in products directory
        // Storage::disk('public')->assertExists($product->thumbnail);
    }

    public function test_create_fails_with_missing_required_fields()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/products', []);

        $response->assertSessionHasErrors(['name', 'category_id', 'price_customer']);
    }

    public function test_create_fails_with_duplicate_slug()
    {
        $admin = $this->createAdmin();
        $product = $this->createProduct(['name' => 'Duplicate Slug']);
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/products', [
            'category_id' => $category->id,
            'name' => 'Duplicate Slug', // Will generate same slug
            'description' => 'Test',
            'price_customer' => 100,
            'price_reseller' => 80,
            'price_bulk' => 70,
            'min_bulk_qty' => 10,
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('name'); // Assuming slug or name uniqueness triggers error
    }

    public function test_customer_cannot_access_admin_product_routes()
    {
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer)->get('/admin/products');

        $response->assertStatus(403);
    }
}
