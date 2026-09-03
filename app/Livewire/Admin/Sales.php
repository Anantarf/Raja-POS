<?php

namespace App\Livewire\Admin;

use App\Models\Location;
use App\Models\Sale;
use App\Services\SaleCancellationService;
use Livewire\Component;
use Livewire\WithPagination;

class Sales extends Component
{
    use WithPagination;

    public $search = '';
    public $startDate = '';
    public $endDate = '';
    public $selectedLocationId = '';

    public $selectedSaleId = null;
    public $showDetailModal = false;

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

    public function updatingSelectedLocationId()
    {
        $this->resetPage();
    }

    public function openDetailModal($saleId)
    {
        $this->selectedSaleId = $saleId;
        $this->showDetailModal = true;
    }

    public function moveToTrash($saleId, SaleCancellationService $cancellationService)
    {
        if (!auth()->user()->can('sales.trash')) {
            $this->dispatch('notify', message: 'Anda tidak memiliki hak akses memindahkan transaksi ke sampah.', type: 'danger');
            return;
        }

        try {
            $sale = Sale::findOrFail($saleId);
            $cancellationService->moveToTrash($sale, auth()->user(), 'Pembatalan manual admin');
            $this->dispatch('notify', message: 'Transaksi berhasil dipindahkan ke Sampah Transaksi dan stok/saldo dikembalikan.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'danger');
        }
    }

    public function render()
    {
        $baseQuery = Sale::where('status', 'COMPLETED')
            ->when($this->startDate, function ($q) {
                $q->whereDate('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($q) {
                $q->whereDate('created_at', '<=', $this->endDate);
            });

        // KPI Summary Stats
        $totalRevenue = (clone $baseQuery)->sum('total_amount');
        $totalTransactions = (clone $baseQuery)->count();
        $averageBasket = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Sales Table Query
        $query = (clone $baseQuery)->with(['cashier', 'user', 'payments.paymentMethod']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('cashier', function ($uq) {
                      $uq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(12);
        $selectedSale = $this->selectedSaleId ? Sale::with(['items', 'payments.paymentMethod', 'cashier', 'user'])->find($this->selectedSaleId) : null;

        return view('livewire.admin.sales', [
            'sales' => $sales,
            'selectedSale' => $selectedSale,
            'locations' => Location::all(),
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'averageBasket' => $averageBasket,
        ])->layout('components.layouts.admin', ['title' => 'Riwayat Penjualan']);
    }
}
