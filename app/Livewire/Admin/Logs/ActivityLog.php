<?php
namespace App\Livewire\Admin\Logs;
use App\Models\ActivityLog as ActivityLogModel;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLog extends Component
{
    use WithPagination;
    public string $search = '';

    public function updatingSearch(): void { $this->resetPage(); }

    public function render()
    {
        $query = ActivityLogModel::with('user');

        if ($this->search) {
            $query->where('action', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$this->search}%"));
        }

        return view('livewire.admin.logs.activity-log', [
            'logs' => $query->latest()->paginate(20),
        ])->layout('layouts.admin', ['title' => 'Activity Logs']);
    }
}
