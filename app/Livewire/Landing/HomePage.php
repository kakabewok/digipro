<?php

namespace App\Livewire\Landing;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\WebsiteSetting;
use Livewire\Component;
use Livewire\WithPagination;

class HomePage extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $categoryFilter = null;

    public string $siteName = '';
    public ?string $siteLogo = null;
    public bool $isMaintenance = false;

    public function mount(): void
    {
        $this->siteName = WebsiteSetting::get('site_name', 'DigiStore');
        $this->siteLogo = WebsiteSetting::get('site_logo');

        $maintenance = WebsiteSetting::get('maintenance_mode', 'false');
        if ($maintenance === 'true' && ! auth()->user()?->hasRole('admin')) {
            $this->isMaintenance = true;
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function filterCategory(?int $categoryId): void
    {
        $this->categoryFilter = $categoryId;
        $this->resetPage();
    }

    public function beli(int $productId): void
    {
        if (! auth()->check()) {
            $this->redirect(route('login'));

            return;
        }

        $product = Product::findOrFail($productId);
        $this->redirect(route('checkout', $product->slug));
    }

    public function render()
    {
        $categories = Category::orderBy('name')->get();

        $products = Product::query()
            ->active()
            ->withCount(['stocks as available_stock_count' => fn ($q) => $q->where('status', 'available')])
            ->with('category')
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->categoryFilter, fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->latest()
            ->paginate(12);

        $totalProducts = Product::active()->count();
        $totalOrders = Order::where('status', 'completed')->count();
        $totalMembers = User::count();

        return view('livewire.landing.home-page', [
            'categories' => $categories,
            'products' => $products,
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'totalMembers' => $totalMembers,
        ])->layout('layouts.landing', [
            'title' => $this->siteName . ' — Platform Produk Digital',
            'metaDescription' => 'Beli produk digital terpercaya di ' . $this->siteName . '. Streaming, game voucher, produktivitas, dan lainnya.',
        ]);
    }
}
