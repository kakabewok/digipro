<?php
namespace App\Livewire\Customer;
use App\Services\DepositService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Deposit extends Component
{
    public int $amount = 0;
    public bool $showQris = false;
    public ?string $qrUrl = null;
    public ?string $checkoutUrl = null;
    public ?string $qrisExpiry = null;
    public ?int $depositId = null;

    protected array $rules = ['amount' => 'required|integer|min:10000|max:10000000'];

    public function createDeposit(): void
    {
        $this->validate();
        $depositService = app(DepositService::class);
        $result = $depositService->create(Auth::user(), $this->amount);

        if ($result['success']) {
            $this->showQris = true;
            $this->qrUrl = $result['qr_url'];
            $this->checkoutUrl = $result['checkout_url'];
            $this->qrisExpiry = $result['deposit']->qris_expired_at?->toIso8601String();
            $this->depositId = $result['deposit']->id;
        } else {
            \Flux::toast(
                text: (string) $result['error'],
                heading: 'Deposit Failed',
                variant: 'danger',
            );
        }
    }

    public function render()
    {
        return view('livewire.customer.deposit')
            ->layout('layouts.app', ['title' => 'Top Up Balance']);
    }
}
