<?php
namespace App\Livewire\Admin\Orders;
use App\Models\Order;
use App\Services\OrderService;
use Livewire\Component;

class Detail extends Component
{
    public Order $order;

    public function mount(int $id): void
    {
        $this->order = Order::with(['user', 'product', 'stock', 'voucher'])->findOrFail($id);
    }

    public function cancelOrder(): void
    {
        if ($this->order->status === 'cancelled' || $this->order->status === 'completed') {
            session()->flash('error', 'Cannot cancel order in this state.');
            return;
        }

        $orderService = app(OrderService::class);
        $orderService->cancelOrder($this->order);
        
        // Refresh model
        $this->order->refresh();
        session()->flash('success', 'Order cancelled successfully.');
    }

    public function render()
    {
        return view('livewire.admin.orders.detail', [
            'order' => $this->order,
        ])->layout('layouts.admin', ['title' => 'Order ' . $this->order->invoice_number]);
    }
}
