<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <flux:heading size="xl" level="1">Deposit History</flux:heading>
        
        <flux:button variant="primary" icon="plus" :href="route('deposits.create')" wire:navigate>
            Top Up Balance
        </flux:button>
    </div>

    <flux:card class="p-0 overflow-hidden">
        @if($deposits->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <flux:icon.banknotes class="mb-4 size-12 text-zinc-300 dark:text-zinc-600" />
                <flux:heading>No deposits found</flux:heading>
                <flux:text class="mb-4 mt-2">You haven't made any deposits yet.</flux:text>
                <flux:button :href="route('deposits.create')" variant="primary" wire:navigate>
                    Make a Deposit
                </flux:button>
            </div>
        @else
            <div class="overflow-x-auto">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Date</flux:table.column>
                        <flux:table.column>Amount</flux:table.column>
                        <flux:table.column>Method</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($deposits as $deposit)
                            <flux:table.row>
                                <flux:table.cell class="whitespace-nowrap text-zinc-500">
                                    {{ $deposit->created_at->format('M d, Y H:i') }}
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-nowrap font-medium text-blue-600 dark:text-blue-400">
                                    + Rp {{ number_format($deposit->amount, 0, ',', '.') }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex items-center gap-2">
                                        <flux:icon.qr-code class="size-4 text-zinc-400" />
                                        QRIS
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    @if($deposit->payment_status === 'paid')
                                        <flux:badge color="success" size="sm">Success</flux:badge>
                                    @elseif($deposit->payment_status === 'pending')
                                        <div class="flex items-center gap-2">
                                            <flux:badge color="warning" size="sm">Pending</flux:badge>
                                            <flux:button size="xs" variant="ghost" :href="$deposit->qris_checkout_url" target="_blank">
                                                Pay
                                            </flux:button>
                                        </div>
                                    @else
                                        <flux:badge color="danger" size="sm">Expired</flux:badge>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
            
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $deposits->links() }}
            </div>
        @endif
    </flux:card>
</div>
