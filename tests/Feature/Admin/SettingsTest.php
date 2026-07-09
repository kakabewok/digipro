<?php

namespace Tests\Feature\Admin;

use App\Models\WebsiteSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    public function test_admin_can_view_settings_page()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin/settings');

        $response->assertStatus(200);
    }

    public function test_admin_can_update_site_name()
    {
        $admin = $this->createAdmin();
        WebsiteSetting::factory()->create(['key' => 'site_name', 'value' => 'Old Name']);

        $response = $this->actingAs($admin)->put('/admin/settings', [
            'settings' => [
                'site_name' => 'New Awesome Site Name'
            ]
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('website_settings', [
            'key' => 'site_name',
            'value' => 'New Awesome Site Name'
        ]);
    }

    public function test_admin_can_upload_logo()
    {
        Storage::fake('public');
        $admin = $this->createAdmin();

        $file = UploadedFile::fake()->image('logo.png');

        $response = $this->actingAs($admin)->post('/admin/settings/logo', [
            'logo' => $file
        ]);

        $response->assertRedirect();
        
        // Assert setting was updated
        $setting = WebsiteSetting::where('key', 'logo')->first();
        $this->assertNotNull($setting);
        
        // Storage::disk('public')->assertExists($setting->value);
    }

    public function test_admin_can_enable_maintenance_mode()
    {
        $admin = $this->createAdmin();

        // Using an endpoint like POST /admin/settings/maintenance
        $response = $this->actingAs($admin)->post('/admin/settings/maintenance', [
            'enabled' => true
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('website_settings', [
            'key' => 'maintenance_mode',
            'value' => '1' // or 'true'
        ]);
    }

    public function test_admin_can_disable_maintenance_mode()
    {
        $admin = $this->createAdmin();
        WebsiteSetting::factory()->create(['key' => 'maintenance_mode', 'value' => '1']);

        $response = $this->actingAs($admin)->post('/admin/settings/maintenance', [
            'enabled' => false
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('website_settings', [
            'key' => 'maintenance_mode',
            'value' => '0' // or 'false'
        ]);
    }

    public function test_maintenance_mode_blocks_customer_access()
    {
        WebsiteSetting::factory()->create(['key' => 'maintenance_mode', 'value' => '1']);
        
        $customer = $this->createCustomer();

        // Need to hit a route protected by the middleware
        $response = $this->actingAs($customer)->get('/dashboard');

        // Usually maintenance mode throws 503
        $response->assertStatus(503);
    }

    public function test_maintenance_mode_does_not_block_admin_access()
    {
        WebsiteSetting::factory()->create(['key' => 'maintenance_mode', 'value' => '1']);
        
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin/dashboard'); // or /admin

        $response->assertStatus(200);
    }

    public function test_customer_cannot_access_settings_page()
    {
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer)->get('/admin/settings');

        $response->assertStatus(403);
    }
}
