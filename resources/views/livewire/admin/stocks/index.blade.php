<div x-on:confirm-modal:confirmed.window="if ($event.detail.action === 'deleteStock') { $wire.deleteStock(...$event.detail.params); }">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <flux:heading size="xl" level="1">Stock Management</flux:heading>
        
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <flux:select wire:model.live="productFilter" placeholder="All Products" class="w-full sm:w-48">
                <flux:select.option value="">All Products</flux:select.option>
                @foreach($products as $product)
                    <flux:select.option value="{{ $product->id }}">{{ $product->name }}</flux:select.option>
                @endforeach
            </flux:select>
            
            <flux:select wire:model.live="statusFilter" placeholder="All Status" class="w-full sm:w-36">
                <flux:select.option value="">All Status</flux:select.option>
                <flux:select.option value="available">Available</flux:select.option>
                <flux:select.option value="sold">Sold</flux:select.option>
            </flux:select>

            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search stock value..." icon="magnifying-glass" clearable class="w-full sm:w-48" />
            
            <flux:button variant="primary" icon="arrow-down-tray" :href="route('admin.stocks.import')" wire:navigate>Import</flux:button>
        </div>
    </div>

    <flux:card class="p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Product</flux:table.column>
                    <flux:table.column>Stock Value</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Added</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($stocks as $stock)
                        <flux:table.row>
                            <flux:table.cell class="font-medium text-sm">
                                {{ $stock->product->name }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="max-w-xs truncate font-mono text-xs text-zinc-600 dark:text-zinc-400" title="{{ $stock->value }}">
                                    {{ Str::limit($stock->value, 50) }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($stock->status === 'available')
                                    <flux:badge color="success" size="sm">Available</flux:badge>
                                @else
                                    <div class="flex flex-col gap-1">
                                        <flux:badge color="zinc" size="sm">Sold</flux:badge>
                                        <span class="text-[10px] text-zinc-500">{{ $stock->sold_at?->format('M d, H:i') }}</span>
                                    </div>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="text-xs text-zinc-500">
                                {{ $stock->created_at->format('M d, Y H:i') }}
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($stock->status === 'available')
                                    <flux:button variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-700" @click="$dispatch('confirm-modal:show', { title: 'Hapus Stok', message: 'Item stok ini akan dihapus dan tidak bisa dikembalikan.', confirmLabel: 'Hapus Stok', variant: 'danger', action: 'deleteStock', params: [{{ $stock->id }}] })" />
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-6 text-zinc-500">No stock found.</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $stocks->links() }}
        </div>
    </flux:card>
</div>
