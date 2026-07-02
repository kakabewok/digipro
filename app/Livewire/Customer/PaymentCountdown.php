<?php
namespace App\Livewire\Customer;
use Livewire\Component;
class PaymentCountdown extends Component
{
    public ?string $qrUrl = null;
    public ?string $checkoutUrl = null;
    public ?string $expiryTime = null;
    public ?int $orderId = null;
    public string $type = 'order'; // 'order' or 'deposit'

    public function render()
    {
        return view('livewire.customer.payment-countdown');
    }
}
