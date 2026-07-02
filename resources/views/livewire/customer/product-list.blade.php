<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <flux:heading size="xl" level="1">Browse Products</flux:heading>
        
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <flux:select wire:model.live="categoryFilter" placeholder="All Categories" class="w-full sm:w-48">
                <flux:select.option value="">All Categories</flux:select.option>
                @foreach($categories as $category)
                    <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search products..." icon="magnifying-glass" class="w-full sm:w-64" clearable />
        </div>
    </div>

    @if($products->isEmpty())
        <flux:card class="flex flex-col items-center justify-center py-16 text-center">
            <flux:icon.magnifying-glass class="mb-4 size-12 text-zinc-300 dark:text-zinc-600" />
            <flux:heading size="lg">No products found</flux:heading>
            <flux:text class="mt-2">Try adjusting your search or filters to find what you're looking for.</flux:text>
            @if($search || $categoryFilter)
                <flux:button class="mt-4" wire:click="$set('search', ''); $set('categoryFilter', '')">Clear Filters</flux:button>
            @endif
        </flux:card>
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($products as $product)
                <flux:card class="group flex flex-col overflow-hidden p-0 transition-shadow hover:shadow-md dark:hover:shadow-zinc-800/50">
                    <a href="{{ route('products.show', $product->slug) }}" wire:navigate class="block aspect-[4/3] overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                        @if($product->thumbnail)
                            <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" />
                        @else
                            <div class="flex h-full items-center justify-center text-zinc-400">
                                <flux:icon.photo class="size-12" />
                            </div>
                        @endif
                    </a>
                    
                    <div class="flex flex-1 flex-col p-4">
                        <div class="mb-2 flex items-center justify-between">
                            <flux:badge size="sm" class="truncate max-w-[120px]">{{ $product->category->name }}</flux:badge>
                            
                            @if($product->available_stock_count > 0)
                                <span class="flex items-center text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                    <span class="mr-1.5 flex size-2 rounded-full bg-emerald-500"></span>
                                    In Stock ({{ $product->available_stock_count }})
                                </span>
                            @else
                                <span class="flex items-center text-xs font-medium text-red-600 dark:text-red-400">
                                    <span class="mr-1.5 flex size-2 rounded-full bg-red-500"></span>
                                    Out of Stock
                                </span>
                            @endif
                        </div>
                        
                        <a href="{{ route('products.show', $product->slug) }}" wire:navigate class="mb-1 block">
                            <h3 class="text-base font-semibold leading-tight text-zinc-900 transition-colors group-hover:text-zinc-600 dark:text-white dark:group-hover:text-zinc-300 line-clamp-2">
                                {{ $product->name }}
                            </h3>
                        </a>
                        
                        <div class="mt-auto pt-4">
                            @if($showResellerPrice)
                                <div class="flex items-end gap-2">
                                    <div class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                        Rp {{ number_format($product->price_reseller, 0, ',', '.') }}
                                    </div>
                                    <div class="text-xs text-zinc-500 line-through mb-1">
                                        Rp {{ number_format($product->price_customer, 0, ',', '.') }}
                                    </div>
                                </div>
                            @else
                                <div class="text-lg font-bold text-zinc-900 dark:text-white">
                                    Rp {{ number_format($product->price_customer, 0, ',', '.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @endif
</div>
