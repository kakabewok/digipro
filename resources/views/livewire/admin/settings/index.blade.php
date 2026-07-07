<div>
    <div class="mb-6">
        <flux:heading size="xl" level="1">Settings</flux:heading>
        <flux:text class="text-zinc-500">Manage global website settings and preferences.</flux:text>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <flux:card>
                <form wire:submit="save" class="space-y-6">
                    <flux:heading size="lg" class="mb-4">General Settings</flux:heading>
                    
                    <div class="space-y-4">
                        <flux:input wire:model="site_name" label="Site Name" required />
                        
                        <flux:textarea wire:model="site_description" label="Site Description" rows="3" />
                        
                        <flux:input wire:model="admin_contact" type="email" label="Admin Contact Email" required />
                    </div>
                    
                    <flux:heading size="lg" class="mb-4 mt-8 pt-8 border-t border-zinc-200 dark:border-zinc-700">Inventory & System</flux:heading>
                    
                    <div class="space-y-4">
                        <flux:input wire:model="low_stock_threshold" type="number" label="Low Stock Threshold" description="You will receive alerts when a product's available stock falls below this number." min="1" required />
                        
                        <flux:switch wire:model="maintenance_mode" label="Maintenance Mode" description="When active, only admins can access the site." />
                    </div>
                    
                    <div class="pt-4">
                        <flux:button type="submit" variant="primary">Save Settings</flux:button>
                    </div>
                </form>
            </flux:card>
        </div>
        
        <div>
            <flux:card class="bg-zinc-50 dark:bg-zinc-900/50">
                <flux:heading size="lg" class="mb-4">Information</flux:heading>
                <div class="space-y-4 text-sm text-zinc-600 dark:text-zinc-400">
                    <p>These settings affect the global behavior of your store.</p>
                    <p>Settings are cached for performance and automatically cleared when updated.</p>
                    <div class="mt-4 rounded-md bg-white p-3 border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
                        <div class="font-medium text-zinc-900 dark:text-zinc-100 mb-1">Environment Check</div>
                        <div class="flex justify-between items-center text-xs">
                            <span>AutoGoPay Configured</span>
                            @if(config('services.autogopay.api_key'))
                                <flux:icon.check-circle class="size-4 text-emerald-500" />
                            @else
                                <flux:icon.x-circle class="size-4 text-red-500" />
                            @endif
                        </div>
                    </div>
                </div>
            </flux:card>
        </div>
    </div>
</div>
