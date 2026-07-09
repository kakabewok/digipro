<?php

namespace Tests\Feature\Customer;

use Tests\TestCase;

class ProductListTest extends TestCase
{
    public function test_guest_can_access_product_list_on_landing_page()
    {
        $response = $this->get('/'); // Assuming landing page has product list

        $response->assertStatus(200);
    }

    public function test_customer_can_access_products()
    {
        $user = $this->createCustomer();

        $response = $this->actingAs($user)->get('/products');

        $response->assertStatus(200);
    }

    public function test_only_active_products_are_shown()
    {
        $activeProduct = $this->createProduct(['name' => 'Active Item', 'status' => 'active']);
        $inactiveProduct = $this->createProduct(['name' => 'Inactive Item', 'status' => 'inactive']);

        $response = $this->get('/products');

        $response->assertSee('Active Item');
        $response->assertDontSee('Inactive Item');
    }

    public function test_inactive_products_are_hidden()
    {
        $inactiveProduct = $this->createProduct(['name' => 'Hidden Item', 'status' => 'inactive']);

        $response = $this->get('/products');

        $response->assertDontSee('Hidden Item');
    }

    public function test_search_by_name_returns_matching_products()
    {
        $this->createProduct(['name' => 'Apple']);
        $this->createProduct(['name' => 'Banana']);

        $response = $this->get('/products?search=Apple');

        $response->assertSee('Apple');
        $response->assertDontSee('Banana');
    }

    public function test_search_with_no_match_returns_empty_state()
    {
        $this->createProduct(['name' => 'Apple']);

        $response = $this->get('/products?search=Orange');

        $response->assertDontSee('Apple');
    }

    public function test_filter_by_category_returns_correct_products()
    {
        $product1 = $this->createProduct(['name' => 'Apple']);
        
        $cat2 = \App\Models\Category::create(['name' => 'Cat2', 'slug' => 'cat2']);
        $product2 = $this->createProduct(['category_id' => $cat2->id, 'name' => 'Banana']);

        $response = $this->get('/products?category=cat2');

        $response->assertSee('Banana');
        $response->assertDontSee('Apple');
    }

    public function test_products_are_paginated()
    {
        for ($i = 0; $i < 15; $i++) {
            $this->createProduct(['name' => "Item $i"]);
        }

        $response = $this->get('/products');

        // Should see first 12 items (based on 12 per page requirement)
        $response->assertSee('Item 0');
        $response->assertSee('Item 11');
        $response->assertDontSee('Item 12');
    }

    public function test_customer_sees_price_customer()
    {
        $user = $this->createCustomer();
        $this->createProduct(['name' => 'Test Item', 'price_customer' => 50000, 'price_reseller' => 40000]);

        $response = $this->actingAs($user)->get('/products');

        $response->assertSee('50.000');
        $response->assertDontSee('40.000');
    }

    public function test_reseller_sees_price_reseller()
    {
        $user = $this->createReseller();
        // Give permission so they can see reseller price
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view reseller price', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        
        $this->createProduct(['name' => 'Test Item', 'price_customer' => 50000, 'price_reseller' => 40000]);

        $response = $this->actingAs($user)->get('/products');

        $response->assertSee('40.000');
    }
}
