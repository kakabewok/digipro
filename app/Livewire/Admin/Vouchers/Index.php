<?php
namespace App\Livewire\Admin\Vouchers;
use App\Models\Voucher;
use App\Services\AuditLogService;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    
    public string $search = '';
    public string $code = '';
    public string $type = 'nominal';
    public float $value = 0;
    public float $min_purchase = 0;
    public int $max_usage = 0;
    public ?string $expired_at = null;
    public ?int $editingId = null;

    protected function rules()
    {
        return [
            'code' => 'required|string|unique:vouchers,code' . ($this->editingId ? ',' . $this->editingId : ''),
            'type' => 'required|in:nominal,percentage',
            'value' => 'required|numeric|min:1' . ($this->type === 'percentage' ? '|max:100' : ''),
            'min_purchase' => 'required|numeric|min:0',
            'max_usage' => 'required|integer|min:0',
            'expired_at' => 'nullable|date',
        ];
    }

    public function updatingSearch(): void { $this->resetPage(); }

    public function save(): void
    {
        $this->validate();
        
        $data = [
            'code' => strtoupper($this->code),
            'type' => $this->type,
            'value' => $this->value,
            'min_purchase' => $this->min_purchase,
            'max_usage' => $this->max_usage,
            'expired_at' => $this->expired_at ? $this->expired_at : null,
        ];
        
        if ($this->editingId) {
            $voucher = Voucher::findOrFail($this->editingId);
            $voucher->update($data);
            app(AuditLogService::class)->logUpdated($voucher);
            session()->flash('success', 'Voucher updated.');
        } else {
            $voucher = Voucher::create($data);
            app(AuditLogService::class)->logCreated($voucher);
            session()->flash('success', 'Voucher created.');
        }
        
        $this->reset(['code', 'type', 'value', 'min_purchase', 'max_usage', 'expired_at', 'editingId']);
    }

    public function edit(int $id): void
    {
        $voucher = Voucher::findOrFail($id);
        $this->editingId = $voucher->id;
        $this->code = $voucher->code;
        $this->type = $voucher->type;
        $this->value = $voucher->value;
        $this->min_purchase = $voucher->min_purchase;
        $this->max_usage = $voucher->max_usage;
        $this->expired_at = $voucher->expired_at ? $voucher->expired_at->format('Y-m-d\TH:i') : null;
    }

    public function deleteVoucher(int $id): void
    {
        $voucher = Voucher::findOrFail($id);
        if ($voucher->used_count > 0) {
            session()->flash('error', 'Cannot delete voucher that has been used.');
            return;
        }
        app(AuditLogService::class)->logDeleted($voucher);
        $voucher->delete();
        session()->flash('success', 'Voucher deleted.');
    }

    public function render()
    {
        return view('livewire.admin.vouchers.index', [
            'vouchers' => Voucher::when($this->search, fn ($q) => $q->where('code', 'like', "%{$this->search}%"))
                ->latest()->paginate(15),
        ])->layout('layouts.admin', ['title' => 'Vouchers']);
    }
}
