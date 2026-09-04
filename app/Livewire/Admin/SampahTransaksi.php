<?php

namespace App\Livewire\Admin;

use App\Models\Sale;
use App\Services\SaleCancellationService;
use Livewire\Component;
use Livewire\WithPagination;

class SampahTransaksi extends Component
{
    use WithPagination;

    public $search = '';

    protected $paginationTheme = 'tailwind';

    public function restoreSale($saleId, SaleCancellationService $cancellationService)
    {
        if (! auth()->user()->can('sales.restore')) {
            $this->dispatch('notify', message: 'Anda tidak memiliki hak akses memulihkan transaksi dari sampah.', type: 'danger');

            return;
        }

        try {
            $sale = Sale::forUserLocation()->findOrFail($saleId);
            $cancellationService->restoreFromTrash($sale, auth()->user());
            $this->dispatch('notify', message: 'Transaksi berhasil dipulihkan dari Sampah Transaksi.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'danger');
        }
    }

    public function render()
    {
        $query = Sale::forUserLocation()
            ->with(['user', 'payments.paymentMethod'])
            ->where('status', 'TRASHED');

        if ($this->search) {
            $query->where('invoice_number', 'like', '%'.$this->search.'%');
        }

        $trashedSales = $query->orderBy('updated_at', 'desc')->paginate(12);

        return view('livewire.admin.sampah-transaksi', [
            'trashedSales' => $trashedSales,
        ])->layout('components.layouts.admin', ['title' => 'Sampah Transaksi']);
    }
}
