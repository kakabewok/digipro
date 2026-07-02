<?php
namespace App\Livewire\Admin;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Deposit as DepositModel;
use App\Models\Stock;
use App\Services\StockService;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $stockService = app(StockService::class);
        $today = now()->startOfDay();

        return view('livewire.admin.dashboard', [
            'totalUsers' => User::count(),
            'totalResellers' => User::role('reseller')->count(),
            'totalProducts' => Product::count(),
            'totalStocks' => Stock::where('status', 'available')->count(),
            'totalDeposits' => DepositModel::where('payment_status', 'paid')->count(),
            'totalOrders' => Order::where('status', 'completed')->count(),
            'revenueToday' => Order::where('status', 'completed')->where('created_at', '>=', $today)->sum('total'),
            'revenueMonth' => Order::where('status', 'completed')->where('created_at', '>=', now()->startOfMonth())->sum('total'),
            'topProducts' => Product::withCount(['stocks as sold_count' => fn ($q) => $q->where('status', 'sold')])->orderByDesc('sold_count')->take(5)->get(),
            'lowStockProducts' => $stockService->getLowStockProducts(5),
        ])->layout('layouts.admin', ['title' => 'Admin Dashboard']);
    }
}
