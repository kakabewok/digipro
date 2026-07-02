<?php
namespace App\Livewire\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DepositHistory extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.customer.deposit-history', [
            'deposits' => Auth::user()->deposits()->latest()->paginate(10),
        ])->layout('layouts.app', ['title' => 'Deposit History']);
    }
}
