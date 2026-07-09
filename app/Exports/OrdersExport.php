<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class OrdersExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        protected ?string $status         = null,
        protected ?string $paymentMethod  = null,
        protected ?string $dateFrom       = null,
        protected ?string $dateTo         = null,
        protected ?string $search         = null,
    ) {}

    public function query()
    {
        return Order::query()
            ->with(['user', 'product'])
            ->when($this->status,
                fn($q) => $q->where('status', $this->status))
            ->when($this->paymentMethod,
                fn($q) => $q->where('payment_method', $this->paymentMethod))
            ->when($this->dateFrom,
                fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,
                fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->search, fn($q) => $q
                ->where('invoice_number', 'like', "%{$this->search}%")
                ->orWhereHas('user',
                    fn($u) => $u->where('name', 'like', "%{$this->search}%"))
                ->orWhereHas('product',
                    fn($p) => $p->where('name', 'like', "%{$this->search}%")))
            ->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return [
            '#',
            'No. Invoice',
            'Nama Customer',
            'Email Customer',
            'Produk',
            'Metode Bayar',
            'Harga',
            'Diskon',
            'Total',
            'Status Pembayaran',
            'Status Order',
            'Tanggal Order',
        ];
    }

    public function map($order): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $order->invoice_number,
            $order->user->name      ?? '-',
            $order->user->email     ?? '-',
            $order->product->name   ?? '-',
            ucfirst($order->payment_method),
            'Rp ' . number_format($order->price, 0, ',', '.'),
            'Rp ' . number_format($order->discount, 0, ',', '.'),
            'Rp ' . number_format($order->total, 0, ',', '.'),
            ucfirst($order->payment_status),
            ucfirst($order->status),
            $order->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row bold + background
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF5865A1'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Data Order';
    }
}
