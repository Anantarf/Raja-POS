<?php

namespace App\Livewire\Admin;

use App\Models\BalanceAccount;
use App\Models\BalanceTransaction;
use App\Services\BalanceService;
use Livewire\Component;
use Livewire\WithPagination;

class Balances extends Component
{
    use WithPagination;

    public $search = '';

    // Action Modals
    public $showModal = null; // 'TRANSFER', 'DEPOSIT', 'WITHDRAWAL', 'ADJUSTMENT'

    public $sourceAccountId = null;
    public $destinationAccountId = null;
    public $amount = 0;
    public $reference_id = '';
    public $description = '';

    protected $paginationTheme = 'tailwind';

    public function openModal($type)
    {
        $this->resetForm();
        $this->showModal = $type;
    }

    public function processTransaction(BalanceService $balanceService)
    {
        $this->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
        ]);

        try {
            if ($this->showModal === 'TRANSFER') {
                $this->validate(['sourceAccountId' => 'required', 'destinationAccountId' => 'required|different:sourceAccountId']);
                $balanceService->transfer($this->sourceAccountId, $this->destinationAccountId, $this->amount, $this->description, auth()->user());
                $msg = 'Transfer saldo antar akun berhasil.';
            } elseif ($this->showModal === 'DEPOSIT') {
                $this->validate(['destinationAccountId' => 'required']);
                $balanceService->deposit($this->destinationAccountId, $this->amount, $this->description, auth()->user());
                $msg = 'Deposit saldo berhasil ditambahkan.';
            } elseif ($this->showModal === 'WITHDRAWAL') {
                $this->validate(['sourceAccountId' => 'required']);
                $balanceService->withdraw($this->sourceAccountId, $this->amount, $this->description, auth()->user());
                $msg = 'Penarikan saldo berhasil dicatat.';
            } elseif ($this->showModal === 'ADJUSTMENT') {
                $this->validate(['destinationAccountId' => 'required']);
                $balanceService->adjustBalance($this->destinationAccountId, $this->amount, $this->description, auth()->user());
                $msg = 'Penyesuaian saldo berhasil dilakukan.';
            }

            $this->showModal = null;
            $this->dispatch('notify', message: $msg, type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'danger');
        }
    }

    private function resetForm()
    {
        $accounts = BalanceAccount::where('status', 'ACTIVE')->get();
        $this->sourceAccountId = $accounts->first()?->id;
        $this->destinationAccountId = $accounts->skip(1)->first()?->id;
        $this->amount = 0;
        $this->reference_id = '';
        $this->description = '';
    }

    public function render()
    {
        $accounts = BalanceAccount::where('status', 'ACTIVE')->get();
        $query = BalanceTransaction::with(['sourceAccount', 'destinationAccount', 'creator', 'user']);

        if ($this->search) {
            $query->where('transaction_number', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.admin.balances', [
            'accounts' => $accounts,
            'transactions' => $transactions,
        ])->layout('components.layouts.admin', ['title' => 'Keuangan & Saldo - Raja POS']);
    }
}
