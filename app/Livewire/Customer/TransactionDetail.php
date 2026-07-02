<?php
namespace App\Livewire\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TransactionDetail extends Component
{
    public Order $order;

    public function mount(string $invoice): void
    {
        $this->order = Order::where('invoice_number', $invoice)
            ->where('user_id', Auth::id())
            ->with(['product', 'stock', 'voucher'])
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.customer.transaction-detail', [
            'order' => $this->order,
        ])->layout('layouts.app', ['title' => 'Order ' . $this->order->invoice_number]);
    }
}
