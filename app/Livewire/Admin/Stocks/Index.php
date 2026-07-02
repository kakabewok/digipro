<?php
namespace App\Livewire\Admin\Stocks;
use App\Models\Product;
use App\Models\Stock;
use App\Services\AuditLogService;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    
    public string $search = '';
    public string $statusFilter = '';
    public string $productFilter = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }
    public function updatingProductFilter(): void { $this->resetPage(); }

    public function deleteStock(int $id): void
    {
        $stock = Stock::findOrFail($id);
        if ($stock->status === 'sold') {
            session()->flash('error', 'Cannot delete sold stock.');
            return;
        }
        app(AuditLogService::class)->logDeleted($stock);
        $stock->delete();
        session()->flash('success', 'Stock item deleted.');
    }

    public function render()
    {
        $query = Stock::with('product');

        if ($this->search) {
            $query->where('value', 'like', "%{$this->search}%")
                ->orWhereHas('product', fn ($q) => $q->where('name', 'like', "%{$this->search}%"));
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->productFilter) {
            $query->where('product_id', $this->productFilter);
        }

        return view('livewire.admin.stocks.index', [
            'stocks' => $query->latest()->paginate(20),
            'products' => Product::orderBy('name')->get(),
        ])->layout('layouts.admin', ['title' => 'Stock Management']);
    }
}
