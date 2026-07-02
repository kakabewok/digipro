<div>
    <div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <flux:heading size="xl" level="1">Dashboard</flux:heading>
        
        <flux:button variant="primary" icon="plus" :href="route('deposits.create')" wire:navigate>
            Top Up Balance
        </flux:button>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Balance Card -->
        <flux:card>
            <div class="flex items-center gap-4">
                <div class="rounded-lg bg-blue-100 p-3 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                    <flux:icon.banknotes variant="solid" class="size-6" />
                </div>
                <div>
                    <flux:text class="text-sm font-medium">My Balance</flux:text>
                    <flux:heading size="lg">Rp {{ $balance }}</flux:heading>
                </div>
            </div>
        </flux:card>

        <!-- Orders Card -->
        <flux:card>
            <div class="flex items-center gap-4">
                <div class="rounded-lg bg-emerald-100 p-3 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                    <flux:icon.shopping-cart variant="solid" class="size-6" />
                </div>
                <div>
                    <flux:text class="text-sm font-medium">Total Orders</flux:text>
                    <flux:heading size="lg">{{ $totalOrders }}</flux:heading>
                </div>
            </div>
        </flux:card>

        <!-- Deposits Card -->
        <flux:card>
            <div class="flex items-center gap-4">
                <div class="rounded-lg bg-purple-100 p-3 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                    <flux:icon.arrow-down-tray variant="solid" class="size-6" />
                </div>
                <div>
                    <flux:text class="text-sm font-medium">Total Deposits</flux:text>
                    <flux:heading size="lg">{{ $totalDeposits }}</flux:heading>
                </div>
            </div>
        </flux:card>
    </div>

    <div class="mt-8">
        <div class="mb-4 flex items-center justify-between">
            <flux:heading size="lg">Recent Orders</flux:heading>
            <flux:button variant="ghost" size="sm" :href="route('orders.index')" wire:navigate>
                View All
            </flux:button>
        </div>

        <flux:card class="overflow-hidden p-0">
            @if($recentOrders->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <flux:icon.inbox class="mb-4 size-12 text-zinc-300 dark:text-zinc-600" />
                    <flux:heading>No orders yet</flux:heading>
                    <flux:text class="mb-4 mt-2">Looks like you haven't made any purchases.</flux:text>
                    <flux:button :href="route('products.index')" variant="primary" wire:navigate>
                        Browse Products
                    </flux:button>
                </div>
            @else
                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($recentOrders as $order)
                        <div class="flex items-center justify-between p-4 transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <div class="flex items-center gap-4">
                                @if($order->product->thumbnail)
                                    <img src="{{ Storage::url($order->product->thumbnail) }}" alt="{{ $order->product->name }}" class="size-12 rounded-md object-cover" />
                                @else
                                    <div class="flex size-12 items-center justify-center rounded-md bg-zinc-100 text-zinc-400 dark:bg-zinc-800">
                                        <flux:icon.cube class="size-6" />
                                    </div>
                                @endif
                                <div>
                                    <div class="font-medium">{{ $order->product->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ $order->invoice_number }} &middot; {{ $order->created_at->format('M d, Y H:i') }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-medium">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                                <div class="mt-1">
                                    @if($order->status === 'completed')
                                        <flux:badge color="success" size="sm">Completed</flux:badge>
                                    @elseif($order->status === 'pending')
                                        <flux:badge color="warning" size="sm">Pending</flux:badge>
                                    @elseif($order->status === 'processing')
                                        <flux:badge color="blue" size="sm">Processing</flux:badge>
                                    @else
                                        <flux:badge color="danger" size="sm">Cancelled</flux:badge>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </flux:card>
    </div>
</div>
