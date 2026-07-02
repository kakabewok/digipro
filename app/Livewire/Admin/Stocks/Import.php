<?php
namespace App\Livewire\Admin\Stocks;
use App\Models\Product;
use App\Services\ActivityLogService;
use App\Services\StockService;
use Livewire\Component;

class Import extends Component
{
    public int $product_id = 0;
    public string $stock_data = ''; // text area input

    protected array $rules = [
        'product_id' => 'required|exists:products,id',
        'stock_data' => 'required|string',
    ];

    public function import(): void
    {
        $this->validate();
        
        $lines = explode("\n", str_replace("\r", "", $this->stock_data));
        
        $stockService = app(StockService::class);
        $count = $stockService->bulkAdd($this->product_id, $lines);
        
        app(ActivityLogService::class)->log('import_stock', "Imported {$count} items for product ID {$this->product_id}");
        
        session()->flash('success', "Successfully imported {$count} stock items.");
        $this->redirect(route('admin.stocks.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.stocks.import', [
            'products' => Product::orderBy('name')->get(),
        ])->layout('layouts.admin', ['title' => 'Import Stock']);
    }
}
