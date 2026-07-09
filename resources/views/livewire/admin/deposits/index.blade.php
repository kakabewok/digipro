<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <flux:heading size="xl" level="1">Deposits</flux:heading>
        
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <flux:select wire:model.live="statusFilter" placeholder="All Status" class="w-full sm:w-48">
                <flux:select.option value="">All Status</flux:select.option>
                <flux:select.option value="pending">Pending</flux:select.option>
                <flux:select.option value="paid">Paid</flux:select.option>
                <flux:select.option value="expired">Expired</flux:select.option>
            </flux:select>

            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search user or ref..." icon="magnifying-glass" clearable class="w-full sm:w-64" />
        </div>
    </div>

    <flux:card class="p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Date</flux:table.column>
                    <flux:table.column>User</flux:table.column>
                    <flux:table.column>Amount</flux:table.column>
                    <flux:table.column>QRIS Reference</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($deposits as $deposit)
                        <flux:table.row>
                            <flux:table.cell class="whitespace-nowrap text-xs text-zinc-500">
                                {{ $deposit->created_at->format('M d, Y H:i') }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="font-medium text-sm">{{ $deposit->user->name }}</div>
                                <div class="text-xs text-zinc-500">{{ $deposit->user->email }}</div>
                            </flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap font-medium text-blue-600 dark:text-blue-400">
                                Rp {{ number_format($deposit->amount, 0, ',', '.') }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="font-mono text-xs text-zinc-500">{{ $deposit->qris_reference ?? '-' }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <x-status-badge :status="$deposit->payment_status" />
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-6 text-zinc-500">No deposits found.</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $deposits->links() }}
        </div>
    </flux:card>
</div>
