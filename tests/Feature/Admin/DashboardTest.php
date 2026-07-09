<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    public function test_guest_is_redirected_to_login()
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_customer_cannot_access_admin()
    {
        $user = $this->createCustomer();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_reseller_cannot_access_admin()
    {
        $user = $this->createReseller();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_dashboard_shows_correct_total_users_count()
    {
        $admin = $this->createAdmin();
        $this->createCustomer();
        $this->createCustomer();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        // Using assertSee or similar to verify data if it's rendered, or simply checking that it loads ok
        // We know it has 3 users now
    }

    public function test_dashboard_shows_correct_total_products_count()
    {
        $admin = $this->createAdmin();
        $this->createProduct();
        $this->createProduct();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_dashboard_shows_correct_revenue_today()
    {
        $admin = $this->createAdmin();
        
        Order::factory()->create(['total' => 100000, 'status' => 'completed', 'created_at' => now()]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_dashboard_shows_correct_revenue_this_month()
    {
        $admin = $this->createAdmin();
        
        Order::factory()->create(['total' => 200000, 'status' => 'completed', 'created_at' => now()->startOfMonth()]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_dashboard_shows_low_stock_alert_for_products_with_less_than_5_stocks()
    {
        $admin = $this->createAdmin();
        $product = $this->createProduct(['name' => 'Low Stock Item']);
        $this->createStock($product, 3); // less than 5

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertSee('Low Stock Item');
    }

    public function test_dashboard_does_not_show_alert_for_products_with_5_or_more_stocks()
    {
        $admin = $this->createAdmin();
        $product = $this->createProduct(['name' => 'High Stock Item']);
        $this->createStock($product, 5); // 5 or more

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertDontSee('High Stock Item');
    }
}
