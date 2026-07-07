<div x-on:confirm-modal:confirmed.window="if ($event.detail.action === 'deleteProduct') { $wire.deleteProduct(...$event.detail.params); }">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <flux:heading size="xl" level="1">Products</flux:heading>
        
        <div class="flex items-center gap-3">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search..." icon="magnifying-glass" clearable class="w-64" />
            <flux:button variant="primary" icon="plus" :href="route('admin.products.create')" wire:navigate>Add Product</flux:button>
        </div>
    </div>

    <flux:card class="p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Product</flux:table.column>
                    <flux:table.column>Pricing (Customer / Reseller / Bulk)</flux:table.column>
                    <flux:table.column>Stock</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($products as $product)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product->thumbnail ? Storage::url($product->thumbnail) : '' }}" class="size-10 rounded bg-zinc-100 object-cover dark:bg-zinc-800" />
                                    <div>
                                        <div class="font-medium line-clamp-1" title="{{ $product->name }}">{{ $product->name }}</div>
                                        <div class="text-xs text-zinc-500">{{ $product->category->name }}</div>
                                    </div>
                                </div>
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
                                @if($product->available_stock_count > 0)
                                    <flux:badge color="success" size="sm">{{ number_format($product->available_stock_count) }} left</flux:badge>
                                @else
                                    <flux:badge color="danger" size="sm">Empty</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <button type="button" wire:click="toggleStatus({{ $product->id }})" class="hover:opacity-80 transition-opacity">
                                    @if($product->status === 'active')
                                        <flux:badge color="success" size="sm">Active</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm">Inactive</flux:badge>
                                    @endif
                                </button>
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
            {{ $products->links() }}
        </div>
    </flux:card>
</div>
