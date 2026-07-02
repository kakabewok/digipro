<div>
    <div class="mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('dashboard')" wire:navigate>Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item :href="route('products.index')" wire:navigate>Products</flux:breadcrumbs.item>
            <flux:breadcrumbs.item :href="route('products.show', $product->slug)" wire:navigate>{{ $product->name }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Checkout</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    @if($showQris)
        <!-- QRIS Payment Flow -->
        <livewire:customer.payment-countdown 
            :qr-url="$qrUrl" 
            :checkout-url="$checkoutUrl" 
            :expiry-time="$qrisExpiry" 
            :order-id="$orderId" 
            type="order" 
        />
    @else
        <!-- Checkout Flow -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Left Column: Product & Voucher -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Product Summary -->
                <flux:card>
                    <flux:heading size="lg" class="mb-4">Order Summary</flux:heading>
                    
                    <div class="flex gap-4">
                        <div class="size-20 shrink-0 overflow-hidden rounded-md bg-zinc-100 dark:bg-zinc-800">
                            @if($product->thumbnail)
                                <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}" class="h-full w-full object-cover" />
                            @else
                                <div class="flex h-full items-center justify-center text-zinc-400">
                                    <flux:icon.cube class="size-8" />
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col justify-center">
                            <flux:heading class="line-clamp-2">{{ $product->name }}</flux:heading>
                            <flux:text class="mt-1 text-sm">{{ $product->category->name }}</flux:text>
                        </div>
                        <div class="flex flex-col justify-center text-right">
                            <div class="font-medium">Rp {{ number_format($price, 0, ',', '.') }}</div>
                            <div class="text-sm text-zinc-500">Qty: 1</div>
                        </div>
                    </div>
                </flux:card>

                <!-- Voucher Code -->
                <flux:card>
                    <flux:heading size="lg" class="mb-4">Apply Voucher</flux:heading>
                    
                    <div class="flex gap-2">
                        <flux:input wire:model="voucherCode" placeholder="Enter voucher code" class="flex-1" />
                        <flux:button wire:click="validateVoucher" wire:loading.attr="disabled">Apply</flux:button>
                    </div>
                    
                    @if($voucherMessage)
                        <div class="mt-2 text-sm {{ $voucherValid ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $voucherMessage }}
                        </div>
                    @endif
                </flux:card>
            </div>

            <!-- Right Column: Payment & Total -->
            <div class="space-y-6">
                <flux:card class="bg-zinc-50 dark:bg-zinc-900/50">
                    <flux:heading size="lg" class="mb-4">Payment Details</flux:heading>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-600 dark:text-zinc-400">Subtotal</span>
                            <span class="font-medium">Rp {{ number_format($price, 0, ',', '.') }}</span>
                        </div>
                        
                        @if($discount > 0)
                            <div class="flex justify-between text-sm text-emerald-600 dark:text-emerald-400">
                                <span>Discount</span>
                                <span>- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        
                        <div class="border-t border-zinc-200 pt-3 dark:border-zinc-700">
                            <div class="flex justify-between">
                                <span class="font-medium">Total Payment</span>
                                <span class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <flux:heading size="md" class="mb-3">Payment Method</flux:heading>
                    
                    <flux:radio.group wire:model="paymentMethod" class="mb-6">
                        <flux:radio value="balance">
                            <x-slot:label>
                                <div class="flex w-full justify-between">
                                    <span>Account Balance</span>
                                    <span class="text-zinc-500">Rp {{ number_format($userBalance, 0, ',', '.') }}</span>
                                </div>
                            </x-slot:label>
                            <x-slot:description>Pay instantly from your wallet balance</x-slot:description>
                        </flux:radio>
                        
                        <flux:radio value="qris" label="QRIS" description="Scan with any E-Wallet or Mobile Banking" />
                    </flux:radio.group>

                    @if($paymentMethod === 'balance' && $userBalance < $total)
                        <flux:text class="mb-4 text-sm text-red-600 dark:text-red-400">
                            Insufficient balance. Please top up or use QRIS.
                        </flux:text>
                        <flux:button variant="primary" class="w-full" disabled>
                            Insufficient Balance
                        </flux:button>
                    @else
                        <flux:button wire:click="processCheckout" variant="primary" size="lg" class="w-full" wire:loading.attr="disabled">
                            Pay Rp {{ number_format($total, 0, ',', '.') }}
                        </flux:button>
                    @endif
                </flux:card>
            </div>
        </div>
    @endif
</div>
