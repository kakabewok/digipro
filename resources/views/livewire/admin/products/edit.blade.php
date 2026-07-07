<div>
    <div class="mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('admin.dashboard')" wire:navigate>Admin</flux:breadcrumbs.item>
            <flux:breadcrumbs.item :href="route('admin.products.index')" wire:navigate>Products</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Edit</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="mb-6">
        <flux:heading size="xl" level="1">Edit Product: {{ $product->name }}</flux:heading>
    </div>

    <form wire:submit="save">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <flux:card>
                    <flux:heading size="lg" class="mb-4">Basic Information</flux:heading>
                    
                    <div class="space-y-6">
                        <flux:input wire:model="name" label="Product Name" required />
                        
                        <flux:select wire:model="category_id" label="Category" required>
                            @foreach($categories as $category)
                                <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        
                        <flux:textarea wire:model="description" label="Description" rows="5" />
                    </div>
                </flux:card>
                
                <flux:card>
                    <flux:heading size="lg" class="mb-4">Pricing Strategy</flux:heading>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <flux:input wire:model="price_customer" type="number" label="Customer Price (IDR)" min="0" required />
                        <flux:input wire:model="price_reseller" type="number" label="Reseller Price (IDR)" min="0" required />
                        
                        <div class="col-span-1 sm:col-span-2 border-t border-zinc-200 pt-6 mt-2 dark:border-zinc-700">
                            <flux:heading size="sm" class="mb-4 text-zinc-500">Bulk Pricing (Optional)</flux:heading>
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <flux:input wire:model="price_bulk" type="number" label="Bulk Price (IDR)" min="0" />
                                <flux:input wire:model="min_bulk_qty" type="number" label="Min. Bulk Quantity" min="1" />
                            </div>
                        </div>
                    </div>
                </flux:card>
            </div>
            
            <div class="space-y-6">
                <flux:card>
                    <flux:heading size="lg" class="mb-4">Publishing</flux:heading>
                    
                    <div class="space-y-6">
                        <flux:radio.group wire:model="status" label="Status" required>
                            <flux:radio value="active" label="Active" />
                            <flux:radio value="inactive" label="Inactive" />
                        </flux:radio.group>
                    </div>
                </flux:card>
                
                <flux:card>
                    <flux:heading size="lg" class="mb-4">Product Image</flux:heading>
                    
                    <div class="space-y-4">
                        @if ($thumbnail)
                            <div class="relative w-full aspect-square rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-700">
                                <img src="{{ $thumbnail->temporaryUrl() }}" class="w-full h-full object-cover">
                                <button type="button" wire:click="$set('thumbnail', null)" class="absolute top-2 right-2 flex size-8 items-center justify-center rounded-full bg-black/50 text-white hover:bg-black/70">
                                    <flux:icon.x-mark class="size-5" />
                                </button>
                            </div>
                        @elseif ($product->thumbnail)
                            <div class="relative w-full aspect-square rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-700">
                                <img src="{{ Storage::url($product->thumbnail) }}" class="w-full h-full object-cover">
                            </div>
                            
                            <div class="flex items-center justify-center w-full mt-2">
                                <label for="dropzone-file" class="text-sm text-blue-600 hover:underline cursor-pointer">
                                    Change Image
                                    <input id="dropzone-file" type="file" class="hidden" wire:model="thumbnail" accept="image/*" />
                                </label>
                            </div>
                        @else
                            <div class="flex items-center justify-center w-full">
                                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-zinc-300 border-dashed rounded-lg cursor-pointer bg-zinc-50 dark:hover:bg-bray-800 dark:bg-zinc-700 hover:bg-zinc-100 dark:border-zinc-600 dark:hover:border-zinc-500 dark:hover:bg-zinc-600 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <flux:icon.cloud-arrow-up class="w-8 h-8 mb-4 text-zinc-500 dark:text-zinc-400" />
                                        <p class="mb-2 text-sm text-zinc-500 dark:text-zinc-400"><span class="font-semibold">Click to upload</span></p>
                                    </div>
                                    <input id="dropzone-file" type="file" class="hidden" wire:model="thumbnail" accept="image/*" />
                                </label>
                            </div>
                        @endif
                        <div wire:loading wire:target="thumbnail" class="text-sm text-blue-600 text-center w-full">Uploading...</div>
                        @error('thumbnail') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                </flux:card>
                
                <flux:button type="submit" variant="primary" class="w-full" wire:loading.attr="disabled">
                    Update Product
                </flux:button>
                <flux:button variant="ghost" class="w-full mt-2" :href="route('admin.products.index')" wire:navigate>
                    Cancel
                </flux:button>
            </div>
        </div>
    </form>
</div>
