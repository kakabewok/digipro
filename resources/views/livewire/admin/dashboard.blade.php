<div>
    <div class="mb-6">
        <flux:heading size="xl" level="1">Admin Dashboard</flux:heading>
        <flux:text class="text-zinc-500">Overview of store performance and key metrics.</flux:text>
    </div>

    <!-- Top Stats Row -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <flux:card class="bg-blue-50 dark:bg-blue-900/10 border-blue-100 dark:border-blue-900/50">
            <div class="flex items-center gap-4">
                <div class="rounded-lg bg-blue-100 p-3 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400">
                    <flux:icon.banknotes variant="solid" class="size-6" />
                </div>
                <div>
                    <flux:text class="text-sm font-medium text-blue-800 dark:text-blue-300">Revenue Today</flux:text>
                    <flux:heading size="lg" class="text-blue-900 dark:text-blue-100">Rp {{ number_format($revenueToday, 0, ',', '.') }}</flux:heading>
                </div>
            </div>
        </flux:card>

        <flux:card class="bg-emerald-50 dark:bg-emerald-900/10 border-emerald-100 dark:border-emerald-900/50">
            <div class="flex items-center gap-4">
                <div class="rounded-lg bg-emerald-100 p-3 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400">
                    <flux:icon.chart-bar variant="solid" class="size-6" />
                </div>
                <div>
                    <flux:text class="text-sm font-medium text-emerald-800 dark:text-emerald-300">Revenue (Month)</flux:text>
                    <flux:heading size="lg" class="text-emerald-900 dark:text-emerald-100">Rp {{ number_format($revenueMonth, 0, ',', '.') }}</flux:heading>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center gap-4">
                <div class="rounded-lg bg-purple-100 p-3 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                    <flux:icon.shopping-cart variant="solid" class="size-6" />
                </div>
                <div>
                    <flux:text class="text-sm font-medium">Completed Orders</flux:text>
                    <flux:heading size="lg">{{ number_format($totalOrders) }}</flux:heading>
                </div>
            </div>
        </flux:card>
        
        <flux:card>
            <div class="flex items-center gap-4">
                <div class="rounded-lg bg-orange-100 p-3 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400">
                    <flux:icon.users variant="solid" class="size-6" />
                </div>
                <div>
                    <flux:text class="text-sm font-medium">Users / Resellers</flux:text>
                    <flux:heading size="lg">{{ number_format($totalUsers) }} / {{ number_format($totalResellers) }}</flux:heading>
                </div>
            </div>
        </flux:card>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Low Stock Alerts -->
        <flux:card>
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <flux:icon.exclamation-triangle class="size-5 text-red-500" />
                    <flux:heading size="lg">Low Stock Alerts</flux:heading>
                </div>
                <flux:button variant="ghost" size="sm" :href="route('admin.stocks.index')" wire:navigate>Manage</flux:button>
            </div>
            
            @if($lowStockProducts->isEmpty())
                <div class="py-8 text-center text-zinc-500">
                    <flux:icon.check-circle class="mx-auto mb-2 size-8 text-emerald-500" />
                    All products have sufficient stock.
                </div>
            @else
                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($lowStockProducts as $product)
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $product->thumbnail ? Storage::url($product->thumbnail) : '' }}" class="size-10 rounded bg-zinc-100 object-cover dark:bg-zinc-800" />
                                <div>
                                    <div class="font-medium line-clamp-1">{{ $product->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ $product->category->name }}</div>
                                </div>
                            </div>
                            <flux:badge color="danger">
                                Only {{ $product->available_stock_count }} left
                            </flux:badge>
                        </div>
                    @endforeach
                </div>
            @endif
        </flux:card>

        <!-- Top Selling Products -->
        <flux:card>
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <flux:icon.star class="size-5 text-yellow-500" />
                    <flux:heading size="lg">Top Selling Products</flux:heading>
                </div>
                <flux:button variant="ghost" size="sm" :href="route('admin.products.index')" wire:navigate>View All</flux:button>
            </div>
            
            @if($topProducts->isEmpty())
                <div class="py-8 text-center text-zinc-500">
                    No sales data available yet.
                </div>
            @else
                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($topProducts as $index => $product)
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex size-8 items-center justify-center rounded-full bg-zinc-100 text-sm font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                    #{{ $index + 1 }}
                                </div>
                                <div>
                                    <div class="font-medium line-clamp-1">{{ $product->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ number_format($product->price_customer, 0, ',', '.') }} IDR</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($product->sold_count) }}</div>
                                <div class="text-xs text-zinc-500">Sold</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </flux:card>
    </div>
</div>
