<?php
namespace App\Livewire\Admin\Products;
use App\Models\Product;
use App\Services\AuditLogService;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public string $search = '';

    public function updatingSearch(): void { $this->resetPage(); }

    public function deleteProduct(int $id): void
    {
        $product = Product::findOrFail($id);
        app(AuditLogService::class)->logDeleted($product);
        $product->delete();
        session()->flash('success', 'Product deleted.');
    }

    public function toggleStatus(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => $product->status === 'active' ? 'inactive' : 'active']);
        app(AuditLogService::class)->logUpdated($product);
    }

    public function render()
    {
        return view('livewire.admin.products.index', [
            'products' => Product::with('category')
                ->withCount(['stocks as available_stock_count' => fn ($q) => $q->where('status', 'available')])
                ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->latest()->paginate(15),
        ])->layout('layouts.admin', ['title' => 'Products']);
    }
}
