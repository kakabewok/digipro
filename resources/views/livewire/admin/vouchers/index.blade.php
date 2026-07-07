<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <flux:heading size="xl" level="1">Vouchers</flux:heading>
        
        <div class="w-full sm:w-64">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search code..." icon="magnifying-glass" clearable />
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 xl:grid-cols-3">
        <!-- List Vouchers -->
        <div class="xl:col-span-2">
            <flux:card class="p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Code</flux:table.column>
                            <flux:table.column>Value</flux:table.column>
                            <flux:table.column>Min. Purchase</flux:table.column>
                            <flux:table.column>Usage</flux:table.column>
                            <flux:table.column>Status</flux:table.column>
                            <flux:table.column></flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @forelse($vouchers as $voucher)
                                <flux:table.row>
                                    <flux:table.cell>
                                        <div class="font-mono font-bold text-blue-600 dark:text-blue-400">{{ $voucher->code }}</div>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        @if($voucher->type === 'nominal')
                                            Rp {{ number_format($voucher->value, 0, ',', '.') }}
                                        @else
                                            {{ $voucher->value }}%
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        Rp {{ number_format($voucher->min_purchase, 0, ',', '.') }}
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <div class="text-sm">
                                            {{ $voucher->used_count }} / {{ $voucher->max_usage > 0 ? $voucher->max_usage : '∞' }}
                                        </div>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        @if(!$voucher->isValid())
                                            <flux:badge color="danger" size="sm">Invalid</flux:badge>
                                        @else
                                            <flux:badge color="success" size="sm">Active</flux:badge>
                                        @endif
                                        @if($voucher->expired_at)
                                            <div class="text-[10px] text-zinc-500 mt-1">Exp: {{ $voucher->expired_at->format('M d, H:i') }}</div>
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <div class="flex items-center gap-2 justify-end">
                                            <flux:button variant="ghost" size="sm" icon="pencil" wire:click="edit({{ $voucher->id }})" />
                                            @if($voucher->used_count === 0)
                                                <flux:button variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-700" wire:click="deleteVoucher({{ $voucher->id }})" wire:confirm="Are you sure?" />
                                            @endif
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="6" class="text-center py-6 text-zinc-500">No vouchers found.</flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </div>
                
                <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
                    {{ $vouchers->links() }}
                </div>
            </flux:card>
        </div>
        
        <!-- Form Create/Edit -->
        <div>
            <flux:card class="sticky top-6">
                <flux:heading size="lg" class="mb-4">{{ $editingId ? 'Edit Voucher' : 'Create Voucher' }}</flux:heading>
                
                <form wire:submit="save" class="space-y-4">
                    <flux:input wire:model="code" label="Voucher Code" placeholder="e.g. SUMMER24" required class="uppercase" />
                    
                    <div class="grid grid-cols-2 gap-4">
                        <flux:select wire:model.live="type" label="Type" required>
                            <flux:select.option value="nominal">Nominal (Rp)</flux:select.option>
                            <flux:select.option value="percentage">Percentage (%)</flux:select.option>
                        </flux:select>
                        
                        <flux:input wire:model="value" type="number" label="Value" min="1" required />
                    </div>
                    
                    <flux:input wire:model="min_purchase" type="number" label="Minimum Purchase (IDR)" min="0" required />
                    
                    <flux:input wire:model="max_usage" type="number" label="Max Usage limit" placeholder="0 for unlimited" min="0" required />
                    
                    <flux:input wire:model="expired_at" type="datetime-local" label="Expiry Date (Optional)" />
                    
                    <div class="flex items-center gap-2 pt-4">
                        <flux:button type="submit" variant="primary" class="flex-1">
                            {{ $editingId ? 'Update Voucher' : 'Create Voucher' }}
                        </flux:button>
                        
                        @if($editingId)
                            <flux:button type="button" variant="ghost" wire:click="$set('editingId', null); $set('code', ''); $set('value', 0)">Cancel</flux:button>
                        @endif
                    </div>
                </form>
            </flux:card>
        </div>
    </div>
</div>
