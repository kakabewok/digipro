<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" level="1">Audit Logs</flux:heading>
            <flux:text class="text-zinc-500">Detailed record of data changes in the system.</flux:text>
        </div>
        
        <div class="w-full sm:w-64">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search model or user..." icon="magnifying-glass" clearable />
        </div>
    </div>

    <flux:card class="p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Date</flux:table.column>
                    <flux:table.column>User</flux:table.column>
                    <flux:table.column>Action</flux:table.column>
                    <flux:table.column>Model</flux:table.column>
                    <flux:table.column>ID</flux:table.column>
                    <flux:table.column></flux:table.column>
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
                                @else
                                    <span class="text-zinc-500 italic">System</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($log->action === 'created')
                                    <flux:badge color="success" size="sm">Created</flux:badge>
                                @elseif($log->action === 'updated')
                                    <flux:badge color="warning" size="sm">Updated</flux:badge>
                                @elseif($log->action === 'deleted')
                                    <flux:badge color="danger" size="sm">Deleted</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">{{ $log->action }}</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="text-xs font-mono">{{ class_basename($log->model_type) }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="text-xs font-mono">{{ $log->model_id }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button variant="ghost" size="sm" wire:click="viewDetails({{ $log->id }})">Details</flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center py-6 text-zinc-500">No audit logs found.</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $logs->links() }}
        </div>
    </flux:card>

    <!-- Details Modal -->
    <flux:modal wire:model="viewLogId" class="md:w-[600px]">
        @if($viewLog)
            <div class="mb-4 flex items-center justify-between border-b border-zinc-200 pb-4 dark:border-zinc-700">
                <flux:heading size="lg">Audit Log Details</flux:heading>
                <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="closeDetails" />
            </div>
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-zinc-500">Action:</span>
                        <span class="font-medium capitalize">{{ $viewLog->action }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500">User:</span>
                        <span class="font-medium">{{ $viewLog->user ? $viewLog->user->name : 'System' }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500">Model:</span>
                        <span class="font-medium font-mono text-xs">{{ $viewLog->model_type }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500">Model ID:</span>
                        <span class="font-medium">{{ $viewLog->model_id }}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-zinc-500">IP Address:</span>
                        <span class="font-medium font-mono text-xs">{{ $viewLog->ip_address ?? '-' }}</span>
                    </div>
                </div>

                @if($viewLog->old_values)
                    <div>
                        <flux:heading size="sm" class="mb-2">Old Values</flux:heading>
                        <div class="rounded-md bg-zinc-50 p-3 font-mono text-xs text-red-600 dark:bg-zinc-900/50 dark:text-red-400 overflow-x-auto">
                            <pre>{{ json_encode($viewLog->old_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                @endif

                @if($viewLog->new_values)
                    <div>
                        <flux:heading size="sm" class="mb-2">New Values</flux:heading>
                        <div class="rounded-md bg-zinc-50 p-3 font-mono text-xs text-emerald-600 dark:bg-zinc-900/50 dark:text-emerald-400 overflow-x-auto">
                            <pre>{{ json_encode($viewLog->new_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </flux:modal>
</div>
