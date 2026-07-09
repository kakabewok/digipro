<?php

namespace Tests\Feature\System;

use App\Models\WebsiteSetting;
use Tests\TestCase;

class MaintenanceModeTest extends TestCase
{
    public function test_landing_page_returns_503_when_maintenance_is_enabled()
    {
        WebsiteSetting::factory()->create(['key' => 'maintenance_mode', 'value' => '1']);

        $response = $this->get('/');

        // Custom middleware should catch this and return 503 or redirect
        // Assuming it's returning a 503 view
        $response->assertStatus(503);
    }

    public function test_customer_dashboard_returns_503_when_maintenance_is_enabled()
    {
        WebsiteSetting::factory()->create(['key' => 'maintenance_mode', 'value' => '1']);
        
        $user = $this->createCustomer();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(503);
    }

    public function test_admin_panel_is_accessible_during_maintenance()
    {
        WebsiteSetting::factory()->create(['key' => 'maintenance_mode', 'value' => '1']);
        
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin'); // or /admin/dashboard

        $response->assertStatus(200);
    }

    public function test_landing_page_is_accessible_when_maintenance_is_disabled()
    {
        WebsiteSetting::factory()->create(['key' => 'maintenance_mode', 'value' => '0']);

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_maintenance_view_shows_site_name_from_settings()
    {
        WebsiteSetting::factory()->create(['key' => 'maintenance_mode', 'value' => '1']);
        WebsiteSetting::factory()->create(['key' => 'site_name', 'value' => 'Custom Maintenance Site']);

        $response = $this->get('/');

        $response->assertStatus(503);
        $response->assertSee('Custom Maintenance Site');
    }
}
