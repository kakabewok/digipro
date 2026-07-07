<?php
namespace App\Livewire\Admin\Products;
use App\Models\Category;
use App\Models\Product;
use App\Services\AuditLogService;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public Product $product;
    public string $name = '';
    public int $category_id = 0;
    public ?string $description = null;
    public $thumbnail;
    public float $price_customer = 0;
    public float $price_reseller = 0;
    public ?float $price_bulk = null;
    public ?int $min_bulk_qty = null;
    public string $status = 'active';

    public function mount(int $id): void
    {
        $this->product = Product::findOrFail($id);
        $this->fill($this->product->only('name', 'category_id', 'description', 'price_customer', 'price_reseller', 'price_bulk', 'min_bulk_qty', 'status'));
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'price_customer' => 'required|numeric|min:0',
            'price_reseller' => 'required|numeric|min:0',
            'price_bulk' => 'nullable|numeric|min:0',
            'min_bulk_qty' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $data = [
            'name' => $this->name, 'category_id' => $this->category_id,
            'description' => $this->description,
            'price_customer' => $this->price_customer, 'price_reseller' => $this->price_reseller,
            'price_bulk' => $this->price_bulk, 'min_bulk_qty' => $this->min_bulk_qty,
            'status' => $this->status,
        ];

        if ($this->thumbnail) {
            $data['thumbnail'] = $this->thumbnail->store('products', 'public');
        }

        $this->product->update($data);
        app(AuditLogService::class)->logUpdated($this->product);
        session()->flash('success', 'Product updated.');
        $this->redirect(route('admin.products.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.products.edit', [
            'categories' => Category::orderBy('name')->get(),
        ])->layout('layouts.admin', ['title' => 'Edit Product']);
    }
}
