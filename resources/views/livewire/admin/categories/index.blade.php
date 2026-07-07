<div x-on:confirm-modal:confirmed.window="if ($event.detail.action === 'deleteCategory') { $wire.deleteCategory(...$event.detail.params); }">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <flux:heading size="xl" level="1">Categories</flux:heading>
        
        <div class="w-full sm:w-64">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search categories..." icon="magnifying-glass" clearable />
        </div>
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
                                        <flux:badge size="sm">{{ $category->products_count }}</flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <div class="flex items-center gap-2 justify-end">
                                            <flux:button variant="ghost" size="sm" icon="pencil" wire:click="edit({{ $category->id }})" />
                                            @if($category->products_count === 0)
                                                <flux:button variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-700" @click="$dispatch('confirm-modal:show', { title: 'Hapus Kategori', message: 'Kategori ini akan dihapus permanen.', confirmLabel: 'Ya, Hapus', variant: 'danger', action: 'deleteCategory', params: [{{ $category->id }}] })" />
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
