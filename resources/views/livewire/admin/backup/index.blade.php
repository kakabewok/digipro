<div>
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
                                        <flux:icon.document-zip class="size-5 text-zinc-400" />
                                        <span class="font-mono text-sm font-medium">{{ $backup['name'] }}</span>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell class="text-zinc-500">{{ $backup['size'] }}</flux:table.cell>
                                <flux:table.cell class="text-zinc-500">{{ $backup['date']->format('M d, Y H:i:s') }}</flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex items-center gap-2 justify-end">
                                        <flux:button variant="ghost" size="sm" icon="arrow-down-tray" wire:click="downloadBackup('{{ $backup['path'] }}')" />
                                        <flux:button variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-700" wire:click="deleteBackup('{{ $backup['path'] }}')" wire:confirm="Are you sure you want to delete this backup file?" />
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
