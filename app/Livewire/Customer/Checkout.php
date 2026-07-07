<?php

namespace App\Livewire\Customer;

use App\Models\Product;
use App\Services\OrderService;
use App\Services\VoucherService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Checkout extends Component
{
    public Product $product;
    public string $voucherCode = '';
    public string $paymentMethod = 'balance';
    public float $discount = 0;
    public string $voucherMessage = '';
    public bool $voucherValid = false;

    // QRIS payment state
    public bool $showQris = false;
    public ?string $qrUrl = null;
    public ?string $checkoutUrl = null;
    public ?string $qrisExpiry = null;
    public ?int $orderId = null;

    public function mount(string $slug): void
    {
        $this->product = Product::where('slug', $slug)
            ->active()
            ->withCount(['stocks as available_stock_count' => fn ($q) => $q->where('status', 'available')])
            ->firstOrFail();
    }

    /**
     * Validate voucher code in real-time.
     */
    public function validateVoucher(): void
    {
        if (empty($this->voucherCode)) {
            $this->discount = 0;
            $this->voucherMessage = '';
            $this->voucherValid = false;
            return;
        }

        $orderService = app(OrderService::class);
        $voucherService = app(VoucherService::class);
        $price = $orderService->getPriceForUser($this->product, Auth::user());

        $result = $voucherService->validate($this->voucherCode, $price);

        if ($result['valid']) {
            $this->discount = $voucherService->calculateDiscount($result['voucher'], $price);
            $this->voucherMessage = 'Voucher applied! Discount: Rp ' . number_format($this->discount, 0, ',', '.');
            $this->voucherValid = true;
        } else {
            $this->discount = 0;
            $this->voucherMessage = $result['error'];
            $this->voucherValid = false;
        }
    }

    /**
     * Process the checkout.
     */
    public function processCheckout(): void
    {
        $user = Auth::user();
        $orderService = app(OrderService::class);
        $voucher = $this->voucherValid ? $this->voucherCode : null;

        if ($this->paymentMethod === 'balance') {
            $result = $orderService->createWithBalance($user, $this->product, 1, $voucher);

            if ($result['success']) {
                session()->flash('success', 'Order placed successfully!');
                $this->redirect(route('orders.show', $result['order']->invoice_number), navigate: true);
            } else {
                \Flux::toast(
                    text: (string) $result['error'],
                    heading: 'Checkout Failed',
                    variant: 'danger',
                );
            }
        } else {
            $result = $orderService->createWithQris($user, $this->product, 1, $voucher);

            if ($result['success']) {
                $this->showQris = true;
                $this->qrUrl = $result['qr_url'];
                $this->checkoutUrl = $result['checkout_url'];
                $this->qrisExpiry = $result['order']->qris_expired_at?->toIso8601String();
                $this->orderId = $result['order']->id;
            } else {
                \Flux::toast(
                    text: (string) $result['error'],
                    heading: 'Checkout Failed',
                    variant: 'danger',
                );
            }
        }
    }

    public function render()
    {
        $orderService = app(OrderService::class);
        $price = $orderService->getPriceForUser($this->product, Auth::user());
        $total = max(0, $price - $this->discount);

        return view('livewire.customer.checkout', [
            'price' => $price,
            'total' => $total,
            'userBalance' => Auth::user()->balance,
        ])->layout('layouts.app', ['title' => 'Checkout']);
    }
}
