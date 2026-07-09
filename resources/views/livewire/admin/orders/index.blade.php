<div>
    <div class="mb-6 flex flex-col gap-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
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

        <div class="flex flex-col sm:flex-row sm:items-center justify-end">
            <div class="flex flex-col items-end">
                <button
                    wire:click="exportExcel"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-[#5865A1] hover:bg-[#4a5790] rounded-md transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg wire:loading.remove wire:target="exportExcel" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    <svg wire:loading wire:target="exportExcel" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                    </svg>
                    <span wire:loading.remove wire:target="exportExcel">Export Excel</span>
                    <span wire:loading wire:target="exportExcel">Mengekspor...</span>
                </button>
                <p class="text-xs text-gray-400 dark:text-gray-600 mt-1">
                    Export mengikuti filter yang aktif
                </p>
            </div>
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
                                <x-status-badge :status="$order->status" />
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
