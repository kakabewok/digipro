<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <flux:heading size="xl" level="1">Orders</flux:heading>
        
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <flux:select wire:model.live="paymentMethodFilter" placeholder="All Methods" class="w-full sm:w-36">
                <flux:select.option value="">All Methods</flux:select.option>
                <flux:select.option value="balance">Balance</flux:select.option>
                <flux:select.option value="qris">QRIS</flux:select.option>
            </flux:select>
            
            <flux:select wire:model.live="statusFilter" placeholder="All Status" class="w-full sm:w-36">
                <flux:select.option value="">All Status</flux:select.option>
                <flux:select.option value="pending">Pending</flux:select.option>
                <flux:select.option value="completed">Completed</flux:select.option>
                <flux:select.option value="cancelled">Cancelled</flux:select.option>
            </flux:select>

            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search invoice or user..." icon="magnifying-glass" clearable class="w-full sm:w-64" />
        </div>
    </div>

    <flux:card class="p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Invoice / Date</flux:table.column>
                    <flux:table.column>Customer</flux:table.column>
                    <flux:table.column>Product</flux:table.column>
                    <flux:table.column>Amount</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($orders as $order)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="font-mono text-sm font-medium">{{ $order->invoice_number }}</div>
                                <div class="text-xs text-zinc-500">{{ $order->created_at->format('M d, H:i') }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="font-medium text-sm">{{ $order->user->name }}</div>
                                <div class="text-xs text-zinc-500">{{ $order->user->email }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="truncate max-w-[200px] text-sm" title="{{ $order->product->name }}">
                                    {{ $order->product->name }}
                                </div>
                                <div class="text-xs text-zinc-500">Qty: {{ $order->quantity }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="font-medium text-blue-600 dark:text-blue-400">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                                <div class="text-[10px] text-zinc-500 uppercase">{{ $order->payment_method }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($order->status === 'completed')
                                    <flux:badge color="success" size="sm">Completed</flux:badge>
                                @elseif($order->status === 'pending')
                                    <flux:badge color="warning" size="sm">Pending</flux:badge>
                                @elseif($order->status === 'processing')
                                    <flux:badge color="blue" size="sm">Processing</flux:badge>
                                @else
                                    <flux:badge color="danger" size="sm">Cancelled</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button variant="ghost" size="sm" :href="route('admin.orders.detail', $order->id)" wire:navigate>
                                    View
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center py-6 text-zinc-500">No orders found.</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $orders->links() }}
        </div>
    </flux:card>
</div>
