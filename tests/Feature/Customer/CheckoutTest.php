<?php

namespace Tests\Feature\Customer;

use Tests\TestCase;

class CheckoutTest extends TestCase
{
    public function test_guest_is_redirected_to_login_when_clicking_buy()
    {
        $product = $this->createProduct();

        // Let's assume checkout is accessible at /checkout/{product} or /products/{product}/checkout
        // We will assume /checkout/{product_slug}
        $response = $this->get('/checkout/' . $product->slug);

        $response->assertRedirect('/login');
    }

    public function test_customer_can_access_checkout_page_for_active_product()
    {
        $user = $this->createCustomer();
        $product = $this->createProduct(['status' => 'active']);
        $this->createStock($product, 1);

        $response = $this->actingAs($user)->get('/checkout/' . $product->slug);

        $response->assertStatus(200);
    }

    public function test_checkout_page_shows_correct_customer_price()
    {
        $user = $this->createCustomer();
        $product = $this->createProduct(['price_customer' => 50000, 'status' => 'active']);
        $this->createStock($product, 1);

        $response = $this->actingAs($user)->get('/checkout/' . $product->slug);

        $response->assertSee('50.000');
    }

    public function test_checkout_page_shows_correct_reseller_price()
    {
        $user = $this->createReseller();
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view reseller price', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        
        $product = $this->createProduct(['price_customer' => 50000, 'price_reseller' => 40000, 'status' => 'active']);
        $this->createStock($product, 1);

        $response = $this->actingAs($user)->get('/checkout/' . $product->slug);

        $response->assertSee('40.000');
        $response->assertDontSee('50.000'); // Depending on UI, but safe to check it shows 40000
    }

    public function test_checkout_fails_if_product_is_inactive()
    {
        $user = $this->createCustomer();
        $product = $this->createProduct(['status' => 'inactive']);

        $response = $this->actingAs($user)->get('/checkout/' . $product->slug);

        // Usually it might return 404 or redirect back with error. Let's assume 404 or redirect.
        // A common practice for inactive products is to return 404
        $this->assertTrue($response->status() === 404 || $response->isRedirect());
    }

    public function test_checkout_fails_if_no_stock_available()
    {
        $user = $this->createCustomer();
        $product = $this->createProduct(['status' => 'active']);
        // No stock created

        $response = $this->actingAs($user)->get('/checkout/' . $product->slug);

        // Usually redirects back with error or shows out of stock page
        $this->assertTrue($response->isRedirect() || $response->status() === 200);
        if ($response->status() === 200) {
            $response->assertSee('Out of stock', false);
        }
    }
}
