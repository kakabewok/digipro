<div x-on:confirm-modal:confirmed.window="
    if ($event.detail.action === 'deleteProduct') { $wire.deleteProduct(...$event.detail.params); }
    if ($event.detail.action === 'toggleStatus') { $wire.toggleStatus(...$event.detail.params); }
">
    <div class="mb-6 flex flex-col gap-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <flux:heading size="xl" level="1">Products</flux:heading>
            
            <div class="flex items-center gap-3">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search..." icon="magnifying-glass" clearable class="w-64" />
                <flux:button variant="primary" icon="plus" :href="route('admin.products.create')" wire:navigate>Add Product</flux:button>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-1 bg-zinc-100 dark:bg-zinc-800 p-1 rounded-lg">
                    <button wire:click="$set('filterStatus', null)" class="px-3 py-1 rounded-md text-sm transition-colors {{ $filterStatus === null ? 'bg-[#5865A1] text-white font-medium shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200' }}">Semua Status</button>
                    <button wire:click="$set('filterStatus', 'active')" class="px-3 py-1 rounded-md text-sm transition-colors {{ $filterStatus === 'active' ? 'bg-[#5865A1] text-white font-medium shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200' }}">Aktif</button>
                    <button wire:click="$set('filterStatus', 'inactive')" class="px-3 py-1 rounded-md text-sm transition-colors {{ $filterStatus === 'inactive' ? 'bg-[#5865A1] text-white font-medium shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200' }}">Nonaktif</button>
                </div>

                <div class="flex items-center gap-1 bg-zinc-100 dark:bg-zinc-800 p-1 rounded-lg">
                    <button wire:click="$set('filterStock', null)" class="px-3 py-1 rounded-md text-sm transition-colors {{ $filterStock === null ? 'bg-[#5865A1] text-white font-medium shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200' }}">Semua Stok</button>
                    <button wire:click="$set('filterStock', 'available')" class="px-3 py-1 rounded-md text-sm transition-colors {{ $filterStock === 'available' ? 'bg-[#5865A1] text-white font-medium shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200' }}">Stok Tersedia</button>
                    <button wire:click="$set('filterStock', 'empty')" class="px-3 py-1 rounded-md text-sm transition-colors {{ $filterStock === 'empty' ? 'bg-[#5865A1] text-white font-medium shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200' }}">Stok Habis</button>
                    <button wire:click="$set('filterStock', 'low')" class="px-3 py-1 rounded-md text-sm transition-colors {{ $filterStock === 'low' ? 'bg-[#5865A1] text-white font-medium shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200' }}">Stok Kritis</button>
                </div>
                
                @if($search !== '' || $filterStatus !== null || $filterStock !== null)
                    <button wire:click="resetFilters" class="text-sm text-gray-400 hover:text-gray-600 underline ml-2">Reset Filter</button>
                @endif
            </div>
        </div>

    </div>

    <flux:card class="p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column class="text-center text-gray-400 dark:text-gray-600 w-10 text-sm">#</flux:table.column>
                    <flux:table.column>Thumbnail</flux:table.column>
                    <flux:table.column>Nama Produk</flux:table.column>
                    <flux:table.column>Kategori</flux:table.column>
                    <flux:table.column>Harga</flux:table.column>
                    <flux:table.column>Stok</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($products as $product)
                        <flux:table.row>
                            <flux:table.cell class="text-center text-gray-400 dark:text-gray-600 w-10 text-sm">
                                {{ ($loop->index + 1) + (($products->currentPage() - 1) * $products->perPage()) }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <img src="{{ $product->thumbnail ? Storage::url($product->thumbnail) : '' }}" class="size-10 rounded bg-zinc-100 object-cover dark:bg-zinc-800" />
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="font-medium line-clamp-1" title="{{ $product->name }}">{{ $product->name }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="text-xs text-zinc-500">{{ $product->category->name }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="text-sm">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($product->price_customer, 0, ',', '.') }}</div>
                                    <div class="text-xs text-zinc-500 flex gap-2 mt-0.5">
                                        <span class="text-blue-600 dark:text-blue-400">R: {{ number_format($product->price_reseller, 0, ',', '.') }}</span>
                                        @if($product->price_bulk > 0)
                                            <span class="text-emerald-600 dark:text-emerald-400">B: {{ number_format($product->price_bulk, 0, ',', '.') }} (≥{{ $product->min_bulk_qty }})</span>
                                        @endif
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($product->available_stock_count === 0)
                                    <span class="text-xs font-semibold bg-red-50 text-red-500 dark:bg-red-500/10 dark:text-red-400 px-2 py-0.5 rounded-md">Habis</span>
                                @elseif($product->available_stock_count > 0 && $product->available_stock_count < $lowStockThreshold)
                                    <div class="flex items-center gap-1" title="Stok hampir habis — segera tambah stok">
                                        <span class="text-sm font-bold text-red-500 dark:text-red-400">{{ number_format($product->available_stock_count) }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-red-500 dark:text-red-400">
                                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @else
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ number_format($product->available_stock_count) }}</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <x-status-badge :status="$product->status" />
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:dropdown align="end">
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="pencil" :href="route('admin.products.edit', $product->id)" wire:navigate>Edit</flux:menu.item>
                                        <flux:menu.item icon="plus-circle" :href="route('admin.stocks.import', ['product_id' => $product->id])" wire:navigate>Add Stock</flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" @click="$dispatch('confirm-modal:show', { title: 'Hapus Produk', message: 'Produk ini akan dihapus permanen beserta seluruh datanya.', confirmLabel: 'Ya, Hapus', variant: 'danger', action: 'deleteProduct', params: [{{ $product->id }}] })" class="text-red-600">Delete</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
        
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                Menampilkan {{ $products->count() }} dari {{ $products->total() }} produk
            </div>
            {{ $products->links() }}
        </div>
    </flux:card>
</div>
