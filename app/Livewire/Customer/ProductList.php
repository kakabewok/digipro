<?php

namespace App\Livewire\Customer;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $categoryFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::active()
            ->with('category')
            ->withCount(['stocks as available_stock_count' => fn ($q) => $q->where('status', 'available')]);

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        return view('livewire.customer.product-list', [
            'products' => $query->paginate(12),
            'categories' => Category::orderBy('name')->get(),
            'showResellerPrice' => Auth::user()->can('view reseller price'),
        ])->layout('layouts.app', ['title' => 'Products']);
    }
}
