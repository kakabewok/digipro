<div x-on:confirm-modal:confirmed.window="if ($event.detail.action === 'deleteBackup') { $wire.deleteBackup(...$event.detail.params); }">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" level="1">Database Backups</flux:heading>
            <flux:text class="text-zinc-500">Manage daily automated database backups.</flux:text>
        </div>
        
        <flux:button variant="primary" icon="play" wire:click="runBackup" wire:loading.attr="disabled">
            Run Backup Now
        </flux:button>
    </div>

    <flux:card class="p-0 overflow-hidden">
        @if(empty($backups))
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <flux:icon.circle-stack class="mb-4 size-12 text-zinc-300 dark:text-zinc-600" />
                <flux:heading>No backups found</flux:heading>
                <flux:text class="mb-4 mt-2">Backups are automatically generated daily at 02:00 AM.</flux:text>
                <flux:button variant="primary" wire:click="runBackup" wire:loading.attr="disabled">
                    Generate First Backup
                </flux:button>
            </div>
        @else
            <div class="overflow-x-auto">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>File Name</flux:table.column>
                        <flux:table.column>Size</flux:table.column>
                        <flux:table.column>Date Created</flux:table.column>
                        <flux:table.column></flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($backups as $backup)
                            <flux:table.row>
                                <flux:table.cell>
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-5 text-zinc-400">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        <span class="font-mono text-sm font-medium">{{ $backup['name'] }}</span>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell class="text-zinc-500">{{ $backup['size'] }}</flux:table.cell>
                                <flux:table.cell class="text-zinc-500">{{ $backup['date']->format('M d, Y H:i:s') }}</flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex items-center gap-2 justify-end">
                                        <flux:button variant="ghost" size="sm" icon="arrow-down-tray" wire:click="downloadBackup('{{ $backup['path'] }}')" />
                                        <flux:button variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-700" @click="$dispatch('confirm-modal:show', { title: 'Hapus Backup', message: 'File backup ini akan dihapus permanen.', confirmLabel: 'Ya, Hapus', variant: 'danger', action: 'deleteBackup', params: ['{{ $backup['path'] }}'] })" />
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        @endif
    </flux:card>
</div>
