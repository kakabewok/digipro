<div x-on:confirm-modal:confirmed.window="if ($event.detail.action === 'deleteCategory') { $wire.deleteCategory(...$event.detail.params); }">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <flux:heading size="xl" level="1">Categories</flux:heading>
        
        <div class="w-full sm:w-64">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search categories..." icon="magnifying-glass" clearable />
        </div>
    </div>

    <div class="mb-4">
        @if(session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="bg-green-50 border border-green-200 text-green-700 dark:bg-green-500/10 dark:border-green-500/20 dark:text-green-400 text-sm px-4 py-3 rounded-md mb-4">
                {{ session('success') }}
            </div>
        @endif

        @error('delete')
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="bg-red-50 border border-red-200 text-red-600 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-400 text-sm px-4 py-3 rounded-md mb-4">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- List Categories -->
        <div class="lg:col-span-2">
            <flux:card class="p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Name</flux:table.column>
                            <flux:table.column>Slug</flux:table.column>
                            <flux:table.column>Products</flux:table.column>
                            <flux:table.column></flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @forelse($categories as $category)
                                <flux:table.row>
                                    <flux:table.cell class="font-medium">{{ $category->name }}</flux:table.cell>
                                    <flux:table.cell class="text-zinc-500 font-mono text-xs">{{ $category->slug }}</flux:table.cell>
                                    <flux:table.cell>
                                        @if($category->products_count === 0)
                                            <span class="text-xs text-gray-400 dark:text-gray-500">0 produk</span>
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $category->products_count }} produk</span>
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <div class="flex items-center gap-2 justify-end">
                                            <flux:button variant="ghost" size="sm" icon="pencil" wire:click="edit({{ $category->id }})" />
                                            @if($category->products_count === 0)
                                                <button
                                                  @click="$dispatch('confirm-modal:show', {
                                                    title: 'Hapus Kategori',
                                                    message: 'Kategori ini akan dihapus permanen. Pastikan tidak ada produk yang menggunakan kategori ini.',
                                                    confirmLabel: 'Ya, Hapus',
                                                    variant: 'danger',
                                                    action: 'deleteCategory',
                                                    params: [{{ $category->id }}]
                                                  })"
                                                  class="text-sm text-red-500 hover:text-red-600 font-medium transition-colors duration-150"
                                                >
                                                  Hapus
                                                </button>
                                            @else
                                                <button
                                                  disabled
                                                  title="Tidak dapat dihapus — masih ada produk di kategori ini"
                                                  class="text-sm text-gray-300 dark:text-gray-700 cursor-not-allowed line-through"
                                                >
                                                  Hapus
                                                </button>
                                            @endif
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="4" class="text-center py-6 text-zinc-500">No categories found.</flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </div>
                
                <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
                    {{ $categories->links() }}
                </div>
            </flux:card>
        </div>
        
        <!-- Form Create/Edit -->
        <div>
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ $editingId ? 'Edit Category' : 'Create Category' }}</flux:heading>
                
                <form wire:submit="save" class="space-y-4">
                    <flux:input wire:model="name" label="Category Name" placeholder="e.g. Streaming App" required />
                    
                    <div class="flex items-center gap-2 pt-2">
                        <flux:button type="submit" variant="primary" class="flex-1">
                            {{ $editingId ? 'Update' : 'Create' }}
                        </flux:button>
                        
                        @if($editingId)
                            <flux:button type="button" variant="ghost" wire:click="$set('editingId', null); $set('name', '')">Cancel</flux:button>
                        @endif
                    </div>
                </form>
            </flux:card>
        </div>
    </div>
</div>
