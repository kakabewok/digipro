<?php

namespace Database\Seeders;

use App\Models\WebsiteSetting;
use Illuminate\Database\Seeder;

class WebsiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name' => 'DigiStore',
            'site_logo' => null,
            'site_favicon' => null,
            'site_banner' => null,
            'admin_contact' => 'admin@digistore.com',
            'maintenance_mode' => 'false',
        ];

        foreach ($settings as $key => $value) {
            WebsiteSetting::set($key, $value);
        }
    }
}
