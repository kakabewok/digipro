<div>
    <div class="mb-6">
        <flux:heading size="xl" level="1">Top Up Balance</flux:heading>
        <flux:text class="text-zinc-500">Add funds to your account for faster checkout.</flux:text>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        @if($showQris)
            <div>
                <livewire:customer.payment-countdown 
                    :qr-url="$qrUrl" 
                    :checkout-url="$checkoutUrl" 
                    :expiry-time="$qrisExpiry" 
                    :order-id="$depositId" 
                    type="deposit" 
                />
            </div>
        @else
            <flux:card>
                <form wire:submit="createDeposit" class="space-y-6">
                    <div class="space-y-2">
                        <flux:label for="amount">Amount (IDR)</flux:label>
                        
                        <!-- Quick Select Buttons -->
                        <div class="grid grid-cols-3 gap-2 mb-4" x-data>
                            <flux:button variant="outline" type="button" @click="$wire.set('amount', 50000)">Rp 50k</flux:button>
                            <flux:button variant="outline" type="button" @click="$wire.set('amount', 100000)">Rp 100k</flux:button>
                            <flux:button variant="outline" type="button" @click="$wire.set('amount', 250000)">Rp 250k</flux:button>
                            <flux:button variant="outline" type="button" @click="$wire.set('amount', 500000)">Rp 500k</flux:button>
                            <flux:button variant="outline" type="button" @click="$wire.set('amount', 1000000)">Rp 1M</flux:button>
                            <flux:button variant="outline" type="button" @click="$wire.set('amount', 2000000)">Rp 2M</flux:button>
                        </div>
                        
                        <flux:input wire:model="amount" id="amount" type="number" min="10000" max="10000000" step="1000" placeholder="Enter amount manually..." icon="banknotes" />
                        <flux:text class="text-xs text-zinc-500">Minimum: Rp 10.000, Maximum: Rp 10.000.000</flux:text>
                    </div>

                    <flux:button type="submit" variant="primary" class="w-full" size="lg" wire:loading.attr="disabled">
                        Proceed to Payment (QRIS)
                    </flux:button>
                </form>
            </flux:card>
        @endif

        <div class="space-y-6">
            <flux:card class="bg-zinc-50 dark:bg-zinc-900/50">
                <div class="flex items-center gap-4 mb-4">
                    <div class="rounded-lg bg-blue-100 p-3 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                        <flux:icon.wallet variant="solid" class="size-6" />
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Current Balance</flux:text>
                        <flux:heading size="xl">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</flux:heading>
                    </div>
                </div>
                <div class="border-t border-zinc-200 pt-4 text-sm text-zinc-500 dark:border-zinc-700">
                    <ul class="list-inside list-disc space-y-1">
                        <li>Deposits are processed instantly via QRIS.</li>
                        <li>Zero transaction fees.</li>
                        <li>Balance is non-refundable.</li>
                    </ul>
                </div>
            </flux:card>
        </div>
    </div>
</div>
