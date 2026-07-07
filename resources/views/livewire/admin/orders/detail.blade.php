<div>
    <div class="mb-4">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('admin.dashboard')" wire:navigate>Admin</flux:breadcrumbs.item>
            <flux:breadcrumbs.item :href="route('admin.orders.index')" wire:navigate>Orders</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $order->invoice_number }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="mb-6 flex items-center justify-between">
        <flux:heading size="xl" level="1">Order Details</flux:heading>
        
        <div class="flex items-center gap-2">
            @if($order->status === 'pending')
                <flux:button variant="danger" icon="x-mark" wire:click="cancelOrder" wire:confirm="Are you sure you want to cancel this order? This will release the locked stock back to available status.">
                    Cancel Order
                </flux:button>
            @endif
            
            @if($order->status === 'completed')
                <flux:badge color="success" size="lg">Completed</flux:badge>
            @elseif($order->status === 'pending')
                <flux:badge color="warning" size="lg">Pending</flux:badge>
            @elseif($order->status === 'processing')
                <flux:badge color="blue" size="lg">Processing</flux:badge>
            @else
                <flux:badge color="danger" size="lg">Cancelled</flux:badge>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <flux:card>
                <flux:heading size="lg" class="mb-4">Item Details</flux:heading>
                <div class="flex gap-4">
                    <div class="size-24 shrink-0 overflow-hidden rounded-md bg-zinc-100 dark:bg-zinc-800">
                        @if($order->product->thumbnail)
                            <img src="{{ Storage::url($order->product->thumbnail) }}" alt="{{ $order->product->name }}" class="h-full w-full object-cover" />
                        @else
                            <div class="flex h-full items-center justify-center text-zinc-400">
                                <flux:icon.cube class="size-8" />
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-1 flex-col justify-center">
                        <h3 class="text-lg font-medium">{{ $order->product->name }}</h3>
                        <div class="mt-1 text-sm text-zinc-500">Category: {{ $order->product->category->name }}</div>
                    </div>
                    <div class="flex flex-col justify-center text-right">
                        <div class="font-medium">Rp {{ number_format($order->price, 0, ',', '.') }}</div>
                        <div class="text-sm text-zinc-500">Qty: {{ $order->quantity }}</div>
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <flux:heading size="lg" class="mb-4">Delivered Stock Item</flux:heading>
                @if($order->stock)
                    <div class="rounded-lg bg-zinc-50 p-4 font-mono text-sm text-zinc-800 ring-1 ring-zinc-200 dark:bg-zinc-900 dark:text-zinc-200 dark:ring-zinc-700 break-all whitespace-pre-wrap">
                        {{ $order->stock->value }}
                    </div>
                    <flux:text class="mt-2 text-xs text-zinc-500">This item is securely bound to this order and marked as sold.</flux:text>
                @else
                    <div class="py-8 text-center text-zinc-500">
                        <flux:icon.inbox class="mx-auto mb-2 size-8 text-zinc-300" />
                        No stock item allocated yet.
                    </div>
                @endif
            </flux:card>
            
            @if($order->notes)
                <flux:card>
                    <flux:heading size="lg" class="mb-2">System Notes</flux:heading>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $order->notes }}</p>
                </flux:card>
            @endif
        </div>

        <!-- Sidebar Details -->
        <div class="space-y-6">
            <flux:card>
                <flux:heading size="lg" class="mb-4">Customer Info</flux:heading>
                <div class="flex items-center gap-3">
                    <flux:avatar :name="$order->user->name" :initials="$order->user->initials()" />
                    <div>
                        <div class="font-medium">{{ $order->user->name }}</div>
                        <div class="text-sm text-zinc-500">{{ $order->user->email }}</div>
                    </div>
                </div>
                <div class="mt-4 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                    <div class="text-xs font-medium text-zinc-500 uppercase tracking-wider mb-2">User Roles</div>
                    <div class="flex flex-wrap gap-1">
                        @foreach($order->user->roles as $role)
                            <flux:badge size="sm">{{ $role->name }}</flux:badge>
                        @endforeach
                    </div>
                </div>
            </flux:card>
            
            <flux:card>
                <flux:heading size="lg" class="mb-4">Payment Summary</flux:heading>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-600 dark:text-zinc-400">Subtotal</span>
                        <span class="font-medium">Rp {{ number_format($order->price * $order->quantity, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($order->discount > 0)
                        <div class="flex justify-between text-sm text-emerald-600 dark:text-emerald-400">
                            <span>Discount ({{ $order->voucher->code }})</span>
                            <span>- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    
                    <div class="border-t border-zinc-200 pt-3 dark:border-zinc-700">
                        <div class="flex justify-between">
                            <span class="font-medium">Total</span>
                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-3 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">Payment Method</span>
                        <span class="font-medium uppercase">{{ $order->payment_method }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">Payment Status</span>
                        <span>
                            @if($order->payment_status === 'paid')
                                <span class="text-emerald-600 font-medium">Paid</span>
                            @elseif($order->payment_status === 'pending')
                                <span class="text-warning-600 font-medium">Pending</span>
                            @else
                                <span class="text-red-600 font-medium capitalize">{{ $order->payment_status }}</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">Order Date</span>
                        <span class="font-medium text-right">{{ $order->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">Invoice</span>
                        <span class="font-medium font-mono text-xs">{{ $order->invoice_number }}</span>
                    </div>
                    
                    @if($order->payment_method === 'qris' && $order->qris_reference)
                        <div class="flex justify-between text-sm border-t border-zinc-100 pt-2 mt-2 dark:border-zinc-800">
                            <span class="text-zinc-500">QRIS Ref</span>
                            <span class="font-medium font-mono text-[10px] text-zinc-400">{{ $order->qris_reference }}</span>
                        </div>
                    @endif
                </div>
            </flux:card>
        </div>
    </div>
</div>
