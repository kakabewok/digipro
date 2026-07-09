<?php
namespace App\Livewire\Admin\Orders;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Index extends Component
{
    use WithPagination;
    
    public string $search = '';
    public string $statusFilter = '';
    public string $paymentMethodFilter = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }
    public function updatingPaymentMethodFilter(): void { $this->resetPage(); }

    public function exportExcel(): BinaryFileResponse
    {
        $this->authorize('manage orders');

        $filename = 'orders-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(
            new OrdersExport(
                status:        $this->statusFilter ?: null,
                paymentMethod: $this->paymentMethodFilter ?: null,
                dateFrom:      null,
                dateTo:        null,
                search:        $this->search ?: null,
            ),
            $filename
        );
    }

    public function render()
    {
        $query = Order::with(['user', 'product']);

        if ($this->search) {
            $query->where('invoice_number', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%"));
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->paymentMethodFilter) {
            $query->where('payment_method', $this->paymentMethodFilter);
        }

        return view('livewire.admin.orders.index', [
            'orders' => $query->latest()->paginate(20),
        ])->layout('layouts.admin', ['title' => 'Orders']);
    }
}
