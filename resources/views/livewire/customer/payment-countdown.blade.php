<div class="flex flex-col items-center justify-center py-12"
    x-data="{
        expiryTime: '{{ $expiryTime }}',
        countdown: '',
        isExpired: false,
        timer: null,
        init() {
            if (!this.expiryTime) return;
            
            const target = new Date(this.expiryTime).getTime();
            
            this.timer = setInterval(() => {
                const now = new Date().getTime();
                const distance = target - now;
                
                if (distance < 0) {
                    clearInterval(this.timer);
                    this.countdown = 'Expired';
                    this.isExpired = true;
                    return;
                }
                
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                this.countdown = minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
            }, 1000);
        }
    }"
>
    <flux:card class="w-full max-w-md text-center shadow-xl">
        <flux:heading size="xl" class="mb-2">Complete Payment</flux:heading>
        <flux:text class="mb-6 text-zinc-500">Scan QR Code using your e-wallet or banking app</flux:text>
        
        <div class="relative mx-auto mb-6 flex size-64 items-center justify-center rounded-xl bg-white p-4 shadow-inner ring-1 ring-zinc-200">
            <template x-if="isExpired">
                <div class="absolute inset-0 flex flex-col items-center justify-center rounded-xl bg-white/90 backdrop-blur-sm z-10">
                    <flux:icon.clock class="mb-2 size-12 text-red-500" />
                    <div class="text-lg font-bold text-red-600">Payment Expired</div>
                </div>
            </template>
            
            @if($qrUrl)
                <img src="{{ $qrUrl }}" alt="QRIS Code" class="h-full w-full object-contain" />
            @else
                <div class="text-zinc-400">Loading QR...</div>
            @endif
        </div>
        
        <div class="mb-6 rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800/50">
            <div class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Time Remaining</div>
            <div class="mt-1 text-3xl font-bold font-mono tracking-wider" 
                 x-text="countdown"
                 :class="isExpired ? 'text-red-500' : 'text-blue-600 dark:text-blue-400'">
                --:--
            </div>
        </div>
        
        <div class="flex flex-col gap-3">
            @if($checkoutUrl)
                <flux:button variant="primary" href="{{ $checkoutUrl }}" target="_blank" class="w-full">
                    Pay via AutoGoPay
                </flux:button>
            @endif
            
            <flux:button 
                variant="ghost" 
                class="w-full" 
                href="{{ $type === 'order' ? route('orders.index') : route('deposits.index') }}" 
                wire:navigate
            >
                View Status
            </flux:button>
        </div>
    </flux:card>
    
    <div class="mt-8 text-center text-sm text-zinc-500">
        Payment will be verified automatically.<br>
        Do not close this page if you are waiting for confirmation.
    </div>
</div>
