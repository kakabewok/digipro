<div>
    <div class="mb-4">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('dashboard')" wire:navigate>Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item :href="route('products.index')" wire:navigate>Products</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $product->name }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
        <!-- Product Image -->
        <div class="overflow-hidden rounded-xl bg-zinc-100 ring-1 ring-zinc-200 dark:bg-zinc-800 dark:ring-zinc-700">
            @if($product->thumbnail)
                <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}" class="w-full h-auto object-cover aspect-square" />
            @else
                <div class="flex aspect-square items-center justify-center text-zinc-400">
                    <flux:icon.photo class="size-24" />
                </div>
            @endif
        </div>

        <!-- Product Details -->
        <div class="flex flex-col">
            <div class="mb-2 flex items-center gap-2">
                <flux:badge>{{ $product->category->name }}</flux:badge>
                
                @if($product->available_stock_count > 0)
                    <flux:badge color="success" size="sm">In Stock ({{ $product->available_stock_count }})</flux:badge>
                @else
                    <flux:badge color="danger" size="sm">Out of Stock</flux:badge>
                @endif
            </div>

            <flux:heading size="2xl" class="mb-4">{{ $product->name }}</flux:heading>

            <div class="mb-6 rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800/50">
                <div class="text-sm text-zinc-500 mb-1">Your Price</div>
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                    Rp {{ number_format($price, 0, ',', '.') }}
                </div>
                
                @if($showResellerPrice)
                    <div class="mt-2 text-sm text-zinc-500">
                        Normal price: <span class="line-through">Rp {{ number_format($product->price_customer, 0, ',', '.') }}</span>
                        <span class="ml-2 text-emerald-600 font-medium">Reseller applied</span>
                    </div>
                @endif
                
                @if($product->price_bulk > 0 && auth()->user()->can('view bulk price'))
                    <div class="mt-3 border-t border-zinc-200 pt-3 dark:border-zinc-700">
                        <div class="text-sm font-medium">Bulk Pricing Available</div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">
                            Buy {{ $product->min_bulk_qty }} or more for Rp {{ number_format($product->price_bulk, 0, ',', '.') }} / item
                        </div>
                    </div>
                @endif
            </div>

            <div class="prose prose-sm prose-zinc mb-8 max-w-none dark:prose-invert">
                <flux:heading size="lg" class="mb-2">Description</flux:heading>
                <div class="whitespace-pre-wrap">{{ $product->description ?? 'No description available for this product.' }}</div>
            </div>

            <div class="mt-auto">
                @if($product->available_stock_count > 0)
                    <flux:button 
                        variant="primary" 
                        class="w-full" 
                        icon="shopping-cart"
                        :href="route('checkout', $product->slug)"
                        wire:navigate
                    >
                        Buy Now
                    </flux:button>
                @else
                    <flux:button variant="filled" class="w-full" disabled>
                        Out of Stock
                    </flux:button>
                @endif
            </div>
        </div>
    </div>
</div>
