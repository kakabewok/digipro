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
    public ?string $filterStatus = null;
    public ?string $filterStock = null;
    public int $lowStockThreshold = 5;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }
    public function updatingFilterStock(): void { $this->resetPage(); }

    public function resetFilters(): void
    {
        $this->filterStatus = null;
        $this->filterStock  = null;
        $this->search       = '';
        $this->resetPage();
    }

    public function deleteProduct(int $id): void
    {
        $product = Product::findOrFail($id);
        app(AuditLogService::class)->logDeleted($product);
        $product->delete();
        session()->flash('success', 'Product deleted.');
    }

    public function toggleStatus(int $id): void
    {
        $this->authorize('manage products');

        $product = Product::findOrFail($id);
        $old     = $product->toArray();

        $product->update([
            'status' => $product->status === 'active' ? 'inactive' : 'active',
        ]);

        app(AuditLogService::class)->logUpdated($product);

        session()->flash('success',
            'Status produk berhasil diubah ke ' . $product->fresh()->status . '.');
    }

    public function render()
    {
        return view('livewire.admin.products.index', [
            'products' => Product::with('category')
                ->withCount(['stocks as available_stock_count' => fn ($q) => $q->where('status', 'available')])
                ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
                ->when($this->filterStock === 'available', fn($q) => $q->whereHas('stocks', fn($s) => $s->where('status', 'available')))
                ->when($this->filterStock === 'empty', fn($q) => $q->whereDoesntHave('stocks', fn($s) => $s->where('status', 'available')))
                ->when($this->filterStock === 'low', fn($q) =>
                    $q->having('available_stock_count', '>', 0)
                      ->having('available_stock_count', '<', $this->lowStockThreshold)
                )
                ->latest()->paginate(15),
        ])->layout('layouts.admin', ['title' => 'Products']);
    }
}
