<?php
namespace App\Livewire\Admin\Settings;
use App\Models\WebsiteSetting;
use App\Services\AuditLogService;
use Livewire\Component;

class Index extends Component
{
    public string $site_name = '';
    public string $site_description = '';
    public string $admin_contact = '';
    public int $low_stock_threshold = 5;
    public bool $maintenance_mode = false;

    public function mount(): void
    {
        $settings = WebsiteSetting::allSettings();
        $this->site_name = $settings['site_name'] ?? 'DigiPro';
        $this->site_description = $settings['site_description'] ?? 'Digital Product Store';
        $this->admin_contact = $settings['admin_contact'] ?? 'admin@digipro.test';
        $this->low_stock_threshold = (int) ($settings['low_stock_threshold'] ?? 5);
        $this->maintenance_mode = (bool) ($settings['maintenance_mode'] ?? false);
    }

    public function save(): void
    {
        $this->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'admin_contact' => 'required|email|max:255',
            'low_stock_threshold' => 'required|integer|min:1',
            'maintenance_mode' => 'required|boolean',
        ]);

        WebsiteSetting::set('site_name', $this->site_name);
        WebsiteSetting::set('site_description', $this->site_description);
        WebsiteSetting::set('admin_contact', $this->admin_contact);
        WebsiteSetting::set('low_stock_threshold', (string) $this->low_stock_threshold);
        WebsiteSetting::set('maintenance_mode', $this->maintenance_mode ? '1' : '0');

        session()->flash('success', 'Settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings.index')
            ->layout('layouts.admin', ['title' => 'Website Settings']);
    }
}
