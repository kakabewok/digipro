<div>
    <div class="mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('admin.dashboard')" wire:navigate>Admin</flux:breadcrumbs.item>
            <flux:breadcrumbs.item :href="route('admin.stocks.index')" wire:navigate>Stocks</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Import</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="mb-6">
        <flux:heading size="xl" level="1">Import Stock</flux:heading>
        <flux:text class="text-zinc-500">Bulk add stock items to a product. Enter one item per line.</flux:text>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <flux:card>
                <form wire:submit="import" class="space-y-6">
                    <flux:select wire:model="product_id" label="Product" required>
                        <flux:select.option value="" disabled>Select a product...</flux:select.option>
                        @foreach($products as $product)
                            <flux:select.option value="{{ $product->id }}">{{ $product->name }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <flux:label for="stock_data">Stock Data</flux:label>
                            <span class="text-xs text-zinc-500" x-data x-text="$wire.stock_data ? $wire.stock_data.split('\n').filter(line => line.trim() !== '').length + ' items detected' : '0 items detected'"></span>
                        </div>
                        <flux:textarea 
                            wire:model.live.debounce.300ms="stock_data" 
                            id="stock_data" 
                            rows="15" 
                            placeholder="username:password&#10;account2:pass2&#10;..." 
                            class="font-mono text-sm" 
                            required 
                        />
                        <flux:text class="mt-2 text-xs text-zinc-500">
                            Separate each item with a new line (Enter). Empty lines will be ignored.
                        </flux:text>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <flux:button type="submit" variant="primary" icon="arrow-down-tray" wire:loading.attr="disabled">
                            Import Stock
                        </flux:button>
                        <flux:button variant="ghost" :href="route('admin.stocks.index')" wire:navigate>Cancel</flux:button>
                    </div>
                </form>
            </flux:card>
        </div>
        
        <div>
            <flux:card class="bg-zinc-50 dark:bg-zinc-900/50">
                <flux:heading size="lg" class="mb-4">How it works</flux:heading>
                <div class="space-y-4 text-sm text-zinc-600 dark:text-zinc-400">
                    <p>When a customer purchases a product, the system will automatically allocate one stock item from this list.</p>
                    
                    <p>The system will use the stock items in the order they were added (oldest first).</p>
                    
                    <div class="rounded-md bg-white p-3 font-mono text-xs shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-800 dark:ring-zinc-700">
                        netflix_user1:pass123<br>
                        netflix_user2:pass456<br>
                        netflix_user3:pass789
                    </div>
                </div>
            </flux:card>
        </div>
    </div>
</div>
