<div>
    <div class="mb-6">
        <flux:heading size="xl" level="1">Profile Settings</flux:heading>
        <flux:text class="text-zinc-500">Manage your account settings and password.</flux:text>
    </div>

    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
        <!-- Profile Information -->
        <flux:card>
            <flux:heading size="lg" class="mb-4">Profile Information</flux:heading>
            
            <form wire:submit="updateProfile" class="space-y-6">
                <flux:input wire:model="name" label="Name" required />
                <flux:input wire:model="email" type="email" label="Email Address" required />
                
                <div class="flex items-center gap-4">
                    <flux:button type="submit" variant="primary">Save Changes</flux:button>
                    
                    <x-action-message class="me-3" on="profile-updated">
                        {{ __('Saved.') }}
                    </x-action-message>
                </div>
            </form>
        </flux:card>

        <!-- Update Password -->
        <flux:card>
            <flux:heading size="lg" class="mb-4">Update Password</flux:heading>
            
            <form wire:submit="updatePassword" class="space-y-6">
                <flux:input wire:model="current_password" type="password" label="Current Password" required />
                <flux:input wire:model="password" type="password" label="New Password" required />
                <flux:input wire:model="password_confirmation" type="password" label="Confirm Password" required />
                
                <div class="flex items-center gap-4">
                    <flux:button type="submit" variant="primary">Update Password</flux:button>
                    
                    <x-action-message class="me-3" on="password-updated">
                        {{ __('Saved.') }}
                    </x-action-message>
                </div>
            </form>
        </flux:card>
    </div>
</div>
