<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use App\Models\Deposit as DepositModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        return view('livewire.customer.dashboard', [
            'balance' => number_format($user->balance, 0, ',', '.'),
            'totalOrders' => $user->orders()->count(),
            'totalDeposits' => $user->deposits()->where('payment_status', 'paid')->count(),
            'recentOrders' => $user->orders()
                ->with('product')
                ->latest()
                ->take(5)
                ->get(),
        ])->layout('layouts.app', ['title' => 'Dashboard']);
    }
}
