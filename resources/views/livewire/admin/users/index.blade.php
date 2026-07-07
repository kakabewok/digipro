<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <flux:heading size="xl" level="1">Users</flux:heading>
        
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <flux:select wire:model.live="roleFilter" placeholder="All Roles" class="w-full sm:w-48">
                <flux:select.option value="">All Roles</flux:select.option>
                <flux:select.option value="admin">Admin</flux:select.option>
                <flux:select.option value="reseller">Reseller</flux:select.option>
                <flux:select.option value="customer">Customer</flux:select.option>
            </flux:select>

            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search name or email..." icon="magnifying-glass" clearable class="w-full sm:w-64" />
        </div>
    </div>

    <flux:card class="p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>User</flux:table.column>
                    <flux:table.column>Balance</flux:table.column>
                    <flux:table.column>Roles</flux:table.column>
                    <flux:table.column>Registered</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($users as $user)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <flux:avatar :name="$user->name" :initials="$user->initials()" size="sm" />
                                    <div>
                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $user->name }}</div>
                                        <div class="text-xs text-zinc-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="font-medium">
                                Rp {{ number_format($user->balance, 0, ',', '.') }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->roles as $role)
                                        @if($role->name === 'admin')
                                            <flux:badge color="purple" size="sm">Admin</flux:badge>
                                        @elseif($role->name === 'reseller')
                                            <flux:badge color="blue" size="sm">Reseller</flux:badge>
                                        @else
                                            <flux:badge color="zinc" size="sm">Customer</flux:badge>
                                        @endif
                                    @endforeach
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="text-xs text-zinc-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:dropdown align="end">
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item wire:click="toggleRole({{ $user->id }}, 'reseller')">
                                            {{ $user->hasRole('reseller') ? 'Remove Reseller Role' : 'Make Reseller' }}
                                        </flux:menu.item>
                                        
                                        @if(auth()->id() !== $user->id)
                                            <flux:menu.item wire:click="toggleRole({{ $user->id }}, 'admin')">
                                                {{ $user->hasRole('admin') ? 'Remove Admin Role' : 'Make Admin' }}
                                            </flux:menu.item>
                                        @endif
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-6 text-zinc-500">No users found.</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $users->links() }}
        </div>
    </flux:card>
</div>
