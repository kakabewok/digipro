<?php
namespace App\Livewire\Admin\Users;
use App\Models\User;
use App\Services\AuditLogService;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    
    public string $search = '';
    public string $roleFilter = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingRoleFilter(): void { $this->resetPage(); }

    public function toggleRole(int $userId, string $role): void
    {
        $user = User::findOrFail($userId);
        
        // Prevent removing own admin role
        if ($user->id === auth()->id() && $role === 'admin' && $user->hasRole('admin')) {
            session()->flash('error', 'You cannot remove your own admin role.');
            return;
        }

        if ($user->hasRole($role)) {
            $user->removeRole($role);
        } else {
            $user->assignRole($role);
        }
        
        app(AuditLogService::class)->logUpdated($user);
    }

    public function render()
    {
        $query = User::with('roles');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        if ($this->roleFilter) {
            $query->role($this->roleFilter);
        }

        return view('livewire.admin.users.index', [
            'users' => $query->latest()->paginate(20),
        ])->layout('layouts.admin', ['title' => 'Users']);
    }
}
