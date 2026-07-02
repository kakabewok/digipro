<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <flux:heading size="xl" level="1">My Orders</flux:heading>
        
        <div class="w-full sm:w-72">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search invoice or product..." icon="magnifying-glass" clearable />
        </div>
    </div>

    <flux:card class="p-0 overflow-hidden">
        @if($orders->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <flux:icon.clipboard-document-list class="mb-4 size-12 text-zinc-300 dark:text-zinc-600" />
                <flux:heading>No orders found</flux:heading>
                <flux:text class="mb-4 mt-2">You haven't placed any orders yet, or no orders match your search.</flux:text>
                @if(!$search)
                    <flux:button :href="route('products.index')" variant="primary" wire:navigate>
                        Browse Products
                    </flux:button>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Invoice</flux:table.column>
                        <flux:table.column>Product</flux:table.column>
                        <flux:table.column>Date</flux:table.column>
                        <flux:table.column>Total</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column></flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($orders as $order)
                            <flux:table.row>
                                <flux:table.cell class="whitespace-nowrap font-medium">
                                    {{ $order->invoice_number }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex items-center gap-3">
                                        @if($order->product->thumbnail)
                                            <img src="{{ Storage::url($order->product->thumbnail) }}" class="size-8 rounded object-cover" />
                                        @else
                                            <div class="flex size-8 items-center justify-center rounded bg-zinc-100 text-zinc-400 dark:bg-zinc-800">
                                                <flux:icon.cube class="size-4" />
                                            </div>
                                        @endif
                                        <div class="truncate max-w-[200px]" title="{{ $order->product->name }}">
                                            {{ $order->product->name }}
                                        </div>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-nowrap text-zinc-500">
                                    {{ $order->created_at->format('M d, Y H:i') }}
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-nowrap font-medium">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
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
                                    <flux:button variant="ghost" size="sm" :href="route('orders.show', $order->invoice_number)" wire:navigate>
                                        View
                                    </flux:button>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
            
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $orders->links() }}
            </div>
        @endif
    </flux:card>
</div>
