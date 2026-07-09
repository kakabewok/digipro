<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    public function test_admin_can_view_all_orders()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin/orders');

        $response->assertStatus(200);
    }

    public function test_admin_can_search_orders_by_invoice_number()
    {
        $admin = $this->createAdmin();
        $order1 = Order::factory()->create(['invoice_number' => 'INV-20231010-ABCDEF']);
        $order2 = Order::factory()->create(['invoice_number' => 'INV-20231011-UVWXYZ']);

        $response = $this->actingAs($admin)->get('/admin/orders?search=ABCDEF');

        $response->assertSee('INV-20231010-ABCDEF');
        $response->assertDontSee('INV-20231011-UVWXYZ');
    }

    public function test_admin_can_search_orders_by_username()
    {
        $admin = $this->createAdmin();
        $user1 = User::factory()->create(['name' => 'John Doe']);
        $user2 = User::factory()->create(['name' => 'Jane Smith']);
        
        $order1 = Order::factory()->create(['user_id' => $user1->id, 'invoice_number' => 'INV-JOHN']);
        $order2 = Order::factory()->create(['user_id' => $user2->id, 'invoice_number' => 'INV-JANE']);

        $response = $this->actingAs($admin)->get('/admin/orders?search=John');

        $response->assertSee('INV-JOHN');
        $response->assertDontSee('INV-JANE');
    }

    public function test_admin_can_filter_orders_by_status()
    {
        $admin = $this->createAdmin();
        $order1 = Order::factory()->create(['status' => 'completed', 'invoice_number' => 'INV-COMPLETED']);
        $order2 = Order::factory()->create(['status' => 'pending', 'invoice_number' => 'INV-PENDING']);

        $response = $this->actingAs($admin)->get('/admin/orders?status=completed');

        $response->assertSee('INV-COMPLETED');
        $response->assertDontSee('INV-PENDING');
    }

    public function test_admin_can_filter_orders_by_payment_method()
    {
        $admin = $this->createAdmin();
        $order1 = Order::factory()->create(['payment_method' => 'balance', 'invoice_number' => 'INV-BAL']);
        $order2 = Order::factory()->create(['payment_method' => 'qris', 'invoice_number' => 'INV-QRIS']);

        $response = $this->actingAs($admin)->get('/admin/orders?payment_method=balance');

        $response->assertSee('INV-BAL');
        $response->assertDontSee('INV-QRIS');
    }

    public function test_admin_can_filter_orders_by_date_range()
    {
        $admin = $this->createAdmin();
        $order1 = Order::factory()->create(['created_at' => Carbon::create(2023, 10, 5), 'invoice_number' => 'INV-OCT5']);
        $order2 = Order::factory()->create(['created_at' => Carbon::create(2023, 11, 5), 'invoice_number' => 'INV-NOV5']);

        $response = $this->actingAs($admin)->get('/admin/orders?date_start=2023-10-01&date_end=2023-10-31');

        $response->assertSee('INV-OCT5');
        $response->assertDontSee('INV-NOV5');
    }

    public function test_admin_can_view_order_detail()
    {
        $admin = $this->createAdmin();
        $order = Order::factory()->create();

        $response = $this->actingAs($admin)->get('/admin/orders/' . $order->id);

        $response->assertStatus(200);
        $response->assertSee($order->invoice_number);
    }

    public function test_admin_can_export_orders_to_excel()
    {
        $this->markTestSkipped('Excel package not installed.');
        // Excel::fake();
        // $admin = $this->createAdmin();
        // $response = $this->actingAs($admin)->get('/admin/orders/export');
        // $response->assertStatus(200);
    }

    public function test_exported_excel_file_contains_correct_headers()
    {
        $this->markTestSkipped('Excel package not installed.');
    }

    public function test_customer_cannot_access_admin_order_routes()
    {
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer)->get('/admin/orders');

        $response->assertStatus(403);
    }
}
