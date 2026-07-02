<?php

namespace App\Livewire\Customer;

use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProductDetail extends Component
{
    public Product $product;

    public function mount(string $slug): void
    {
        $this->product = Product::where('slug', $slug)
            ->active()
            ->withCount(['stocks as available_stock_count' => fn ($q) => $q->where('status', 'available')])
            ->firstOrFail();
    }

    public function render()
    {
        $user = Auth::user();
        $orderService = app(OrderService::class);

        return view('livewire.customer.product-detail', [
            'price' => $orderService->getPriceForUser($this->product, $user),
            'showResellerPrice' => $user->can('view reseller price'),
        ])->layout('layouts.app', ['title' => $this->product->name]);
    }
}
