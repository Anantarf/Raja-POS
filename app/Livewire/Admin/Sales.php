<?php

namespace App\Livewire\Admin;

use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Services\SaleCancellationService;
use App\Traits\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;

class Sales extends Component
{
    use WithPagination, WithSorting;

    public $search = '';
    public $startDate = '';
    public $endDate = '';
    public $paymentMethodId = '';

    public $selectedSaleId = null;
    public $showDetailModal = false;

    public $receiptSaleId = null;
    public $showReceiptModal = false;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStartDate()
    {
        $this->resetPage();
    }

    public function updatingEndDate()
    {
        $this->resetPage();
    }

    public function updatingPaymentMethodId()
    {
        $this->resetPage();
    }

    public function openDetailModal($saleId)
    {
        $this->selectedSaleId = $saleId;
        $this->showDetailModal = true;
    }

    public function openReceiptModal($saleId)
    {
        $this->receiptSaleId = $saleId;
        $this->showReceiptModal = true;
    }

    public function moveToTrash($saleId, SaleCancellationService $cancellationService)
    {
        if (! auth()->user()->can('sales.trash')) {
            $this->dispatch('notify', message: 'Anda tidak memiliki hak akses memindahkan transaksi ke sampah.', type: 'danger');

            return;
        }

        try {
            $sale = Sale::forUserLocation()->findOrFail($saleId);
            $cancellationService->moveToTrash($sale, auth()->user(), 'Pembatalan manual admin');
            $this->dispatch('notify', message: 'Transaksi berhasil dipindahkan ke Sampah Transaksi dan stok/saldo dikembalikan.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'danger');
        }
    }

    public function render()
    {
        $query = Sale::forUserLocation()
            ->where('status', 'COMPLETED')
            ->when($this->startDate, function ($q) {
                $q->whereDate('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($q) {
                $q->whereDate('created_at', '<=', $this->endDate);
            })
            ->when($this->paymentMethodId, function ($q) {
                $q->whereHas('payments', function ($pq) {
                    $pq->where('payment_method_id', $this->paymentMethodId);
                });
            })
            ->with(['cashier', 'user', 'payments.paymentMethod']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%'.$this->search.'%')
                    ->orWhereHas('cashier', function ($uq) {
                        $uq->where('name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        $allowedSorts = ['invoice_number', 'total_amount', 'grand_total', 'status', 'created_at'];
        $field = in_array($this->sortField, $allowedSorts) ? $this->sortField : 'created_at';
        if ($field === 'grand_total') {
            $field = 'total_amount';
        }
        $direction = in_array($this->sortDirection, ['asc', 'desc']) ? $this->sortDirection : 'desc';

        $sales = $query->orderBy($field, $direction)->paginate(12);
        $selectedSale = $this->selectedSaleId ? Sale::forUserLocation()->with(['items', 'payments.paymentMethod', 'cashier', 'user'])->find($this->selectedSaleId) : null;
        $receiptSale = $this->receiptSaleId ? Sale::forUserLocation()->with(['items', 'payments.paymentMethod', 'cashier', 'user'])->find($this->receiptSaleId) : null;

        return view('livewire.admin.sales', [
            'sales' => $sales,
            'selectedSale' => $selectedSale,
            'receiptSale' => $receiptSale,
            'paymentMethods' => PaymentMethod::all(),
        ])->layout('components.layouts.admin', ['title' => 'Riwayat Transaksi']);
    }
}
