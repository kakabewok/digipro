<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" level="1">Activity Logs</flux:heading>
            <flux:text class="text-zinc-500">Record of user actions in the system.</flux:text>
        </div>
        
        <div class="w-full sm:w-64">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search action or user..." icon="magnifying-glass" clearable />
        </div>
    </div>

    <flux:card class="p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Date</flux:table.column>
                    <flux:table.column>User</flux:table.column>
                    <flux:table.column>Action</flux:table.column>
                    <flux:table.column>Description</flux:table.column>
                    <flux:table.column>IP Address</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($logs as $log)
                        <flux:table.row>
                            <flux:table.cell class="whitespace-nowrap text-xs text-zinc-500">
                                {{ $log->created_at->format('M d, Y H:i:s') }}
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($log->user)
                                    <div class="font-medium text-sm">{{ $log->user->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ $log->user->email }}</div>
                                @else
                                    <span class="text-zinc-500 italic">System</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" class="uppercase text-[10px]">{{ $log->action }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="text-sm">
                                {{ $log->description }}
                            </flux:table.cell>
                            <flux:table.cell class="font-mono text-xs text-zinc-500">
                                {{ $log->ip_address ?? '-' }}
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-6 text-zinc-500">No activity logs found.</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $logs->links() }}
        </div>
    </flux:card>
</div>
