<?php
namespace App\Livewire\Admin\Categories;
use App\Models\Category;
use App\Services\AuditLogService;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public string $search = '';
    public string $name = '';
    public ?int $editingId = null;

    protected array $rules = ['name' => 'required|string|max:255'];

    public function updatingSearch(): void { $this->resetPage(); }

    public function save(): void
    {
        $this->validate();
        
        if ($this->editingId) {
            $category = Category::findOrFail($this->editingId);
            $category->update(['name' => $this->name]);
            app(AuditLogService::class)->logUpdated($category);
            session()->flash('success', 'Category updated.');
        } else {
            $category = Category::create(['name' => $this->name]);
            app(AuditLogService::class)->logCreated($category);
            session()->flash('success', 'Category created.');
        }
        
        $this->reset(['name', 'editingId']);
    }

    public function edit(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->editingId = $category->id;
        $this->name = $category->name;
    }

    public function deleteCategory(int $id): void
    {
        // Use existing method name from the UI, but implement the new logic
        $this->authorize('manage categories');

        $category = Category::findOrFail($id);

        // Block delete if category has products
        if ($category->products()->exists()) {
            $this->addError('delete',
                'Kategori tidak dapat dihapus karena masih memiliki produk.');
            return;
        }

        $category->delete();

        // Log to audit log
        app(AuditLogService::class)->logDeleted($category);

        session()->flash('success', 'Kategori berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.categories.index', [
            'categories' => Category::withCount('products')
                ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->orderBy('name')->paginate(15),
        ])->layout('layouts.admin', ['title' => 'Categories']);
    }
}
