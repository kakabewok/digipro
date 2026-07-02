<?php
namespace App\Livewire\Admin\Deposits;
use App\Models\Deposit;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    
    public string $search = '';
    public string $statusFilter = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }

    public function render()
    {
        $query = Deposit::with('user');

        if ($this->search) {
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%"))
                ->orWhere('qris_reference', 'like', "%{$this->search}%");
        }

        if ($this->statusFilter) {
            $query->where('payment_status', $this->statusFilter);
        }

        return view('livewire.admin.deposits.index', [
            'deposits' => $query->latest()->paginate(20),
        ])->layout('layouts.admin', ['title' => 'Deposits']);
    }
}
