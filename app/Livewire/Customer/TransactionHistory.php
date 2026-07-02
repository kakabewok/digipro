<?php
namespace App\Livewire\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionHistory extends Component
{
    use WithPagination;
    public string $search = '';

    public function updatingSearch(): void { $this->resetPage(); }

    public function render()
    {
        $orders = Auth::user()->orders()
            ->with('product')
            ->when($this->search, fn ($q) => $q->where('invoice_number', 'like', "%{$this->search}%")
                ->orWhereHas('product', fn ($pq) => $pq->where('name', 'like', "%{$this->search}%")))
            ->latest()
            ->paginate(10);

        return view('livewire.customer.transaction-history', [
            'orders' => $orders,
        ])->layout('layouts.app', ['title' => 'My Orders']);
    }
}
