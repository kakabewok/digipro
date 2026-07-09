<div>
    <div class="mb-4">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('dashboard')" wire:navigate>Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item :href="route('orders.index')" wire:navigate>My Orders</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $order->invoice_number }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="mb-6 flex items-center justify-between">
        <flux:heading size="xl" level="1">Order Details</flux:heading>
        
        <x-status-badge :status="$order->status" />
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            @if($order->status === 'pending' && $order->payment_method === 'qris')
                <div class="rounded-xl border border-warning-200 bg-warning-50 p-6 dark:border-warning-900/50 dark:bg-warning-900/20">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex size-10 items-center justify-center rounded-full bg-warning-100 text-warning-600 dark:bg-warning-900/50 dark:text-warning-400">
                            <flux:icon.clock class="size-6" />
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-warning-800 dark:text-warning-300">Awaiting Payment</h3>
                            <p class="text-sm text-warning-600 dark:text-warning-400">Please complete your payment before the timer expires.</p>
                        </div>
                    </div>
                    
                    <flux:button variant="primary" class="w-full" :href="$order->qris_checkout_url" target="_blank">
                        Pay Now via AutoGoPay
                    </flux:button>
                </div>
            @endif

            @if($order->canShowStockValue())
                <!-- The Digital Product Value -->
                <flux:card class="border-emerald-200 bg-emerald-50/50 dark:border-emerald-900/30 dark:bg-emerald-900/10">
                    <div class="flex items-center gap-2 mb-4 text-emerald-600 dark:text-emerald-400">
                        <flux:icon.check-circle class="size-6" />
                        <flux:heading size="lg">Your Digital Product</flux:heading>
                    </div>
                    
                    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-800 relative">
                        <pre class="font-mono text-sm whitespace-pre-wrap break-all text-zinc-800 dark:text-zinc-200">{{ $order->stock->value }}</pre>
                        
                        <div class="absolute top-2 right-2"
                             x-data="{ copied: false }"
                        >
                            <flux:button variant="ghost" size="sm" icon="clipboard-document"
                                @click="
                                    navigator.clipboard.writeText(`{{ addslashes($order->stock->value) }}`);
                                    copied = true;
                                    setTimeout(() => copied = false, 2000);
                                "
                                x-text="copied ? 'Copied!' : 'Copy'"
                                x-bind:class="copied ? '!text-emerald-600' : ''"
                            >
                            </flux:button>
                        </div>
                    </div>
                    <flux:text class="mt-3 text-sm text-zinc-500">Please save this information securely.</flux:text>
                </flux:card>
            @endif

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
                        <div class="mt-1 text-sm text-zinc-500">Quantity: {{ $order->quantity }}</div>
                    </div>
                    <div class="flex flex-col justify-center text-right">
                        <div class="font-medium">Rp {{ number_format($order->price, 0, ',', '.') }}</div>
                    </div>
                </div>
            </flux:card>
        </div>

        <!-- Sidebar Details -->
        <div class="space-y-6">
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
                            <x-status-badge :status="$order->payment_status" />
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
                </div>
            </flux:card>
        </div>
    </div>
</div>
