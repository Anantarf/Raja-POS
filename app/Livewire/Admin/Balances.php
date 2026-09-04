<?php

namespace App\Livewire\Admin;

use App\Models\BalanceAccount;
use App\Models\BalanceTransaction;
use App\Services\BalanceService;
use App\Traits\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;

class Balances extends Component
{
    use WithPagination, WithSorting;

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
        abort_unless(auth()->user()->can('balance.adjust'), 403);

        if (! in_array($type, ['TRANSFER', 'DEPOSIT', 'WITHDRAWAL', 'ADJUSTMENT'], true)) {
            abort(404);
        }

        $this->resetForm();
        $this->showModal = $type;
    }

    public function processTransaction(BalanceService $balanceService)
    {
        abort_unless(auth()->user()->can('balance.adjust'), 403);
        $this->validate([
            'showModal' => 'required|in:TRANSFER,DEPOSIT,WITHDRAWAL,ADJUSTMENT',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
        ]);

        try {
            $user = auth()->user();
            if ($this->showModal === 'TRANSFER') {
                $this->validate(['sourceAccountId' => 'required', 'destinationAccountId' => 'required|different:sourceAccountId']);
                $source = BalanceAccount::forUserLocation($user)->findOrFail($this->sourceAccountId);
                $dest = BalanceAccount::forUserLocation($user)->findOrFail($this->destinationAccountId);
                $balanceService->transfer($source, $dest, $this->amount, $this->description, $user);
                $msg = 'Transfer saldo antar akun berhasil.';
            } elseif ($this->showModal === 'DEPOSIT') {
                $this->validate(['destinationAccountId' => 'required']);
                $dest = BalanceAccount::forUserLocation($user)->findOrFail($this->destinationAccountId);
                $balanceService->deposit($dest, $this->amount, $this->description, $user);
                $msg = 'Deposit saldo berhasil ditambahkan.';
            } elseif ($this->showModal === 'WITHDRAWAL') {
                $this->validate(['sourceAccountId' => 'required']);
                $source = BalanceAccount::forUserLocation($user)->findOrFail($this->sourceAccountId);
                $balanceService->withdraw($source, $this->amount, $this->description, $user);
                $msg = 'Penarikan saldo berhasil dicatat.';
            } elseif ($this->showModal === 'ADJUSTMENT') {
                $this->validate(['destinationAccountId' => 'required']);
                $dest = BalanceAccount::forUserLocation($user)->findOrFail($this->destinationAccountId);
                $balanceService->adjustBalance($dest, $this->amount, $this->description, $user);
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
        $accounts = BalanceAccount::forUserLocation()->where('status', 'ACTIVE')->where('account_type', '!=', 'PROVIDER')->get();
        $this->sourceAccountId = $accounts->first()?->id;
        $this->destinationAccountId = $accounts->skip(1)->first()?->id;
        $this->amount = 0;
        $this->reference_id = '';
        $this->description = '';
    }

    public $filterType = 'ALL'; // 'ALL', 'IN', 'OUT', 'TRANSFER'

    public function setFilterType($type)
    {
        $this->filterType = $type;
        $this->resetPage();
    }

    public function render()
    {
        $accounts = BalanceAccount::forUserLocation()->where('status', 'ACTIVE')->where('account_type', '!=', 'PROVIDER')->get();
        $query = BalanceTransaction::forUserLocation()->with(['sourceAccount', 'destinationAccount', 'creator', 'user']);

        if ($this->filterType === 'IN') {
            $query->where(function ($q) {
                $q->whereNull('source_account_id')->whereNotNull('destination_account_id');
            });
        } elseif ($this->filterType === 'OUT') {
            $query->where(function ($q) {
                $q->whereNotNull('source_account_id')->whereNull('destination_account_id');
            });
        } elseif ($this->filterType === 'TRANSFER') {
            $query->where(function ($q) {
                $q->whereNotNull('source_account_id')->whereNotNull('destination_account_id');
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('transaction_number', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        $allowedSorts = ['transaction_number', 'amount', 'created_at'];
        $field = in_array($this->sortField, $allowedSorts) ? $this->sortField : 'created_at';
        $direction = in_array($this->sortDirection, ['asc', 'desc']) ? $this->sortDirection : 'desc';

        $transactions = $query->orderBy($field, $direction)->paginate(10);
        $totalBalance = $accounts->sum('balance');
        $totalCash = $accounts->where('account_type', 'CASH')->sum('balance');
        $totalBank = $accounts->whereIn('account_type', ['BANK', 'QRIS'])->sum('balance');
        $totalEwallet = $accounts->where('account_type', 'E_WALLET')->sum('balance');

        return view('livewire.admin.balances', [
            'accounts' => $accounts,
            'transactions' => $transactions,
            'totalBalance' => $totalBalance,
            'totalCash' => $totalCash,
            'totalBank' => $totalBank,
            'totalEwallet' => $totalEwallet,
        ])->layout('components.layouts.admin', ['title' => 'Monitoring Saldo']);
    }
}
