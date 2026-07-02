<?php
namespace App\Livewire\Admin\Logs;
use App\Models\AuditLog as AuditLogModel;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLog extends Component
{
    use WithPagination;
    public string $search = '';
    public ?int $viewLogId = null;

    public function updatingSearch(): void { $this->resetPage(); }

    public function viewDetails(int $id): void
    {
        $this->viewLogId = $id;
    }

    public function closeDetails(): void
    {
        $this->viewLogId = null;
    }

    public function render()
    {
        $query = AuditLogModel::with('user');

        if ($this->search) {
            $query->where('model_type', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$this->search}%"));
        }

        $viewLog = $this->viewLogId ? AuditLogModel::with('user')->find($this->viewLogId) : null;

        return view('livewire.admin.logs.audit-log', [
            'logs' => $query->latest()->paginate(20),
            'viewLog' => $viewLog,
        ])->layout('layouts.admin', ['title' => 'Audit Logs']);
    }
}
