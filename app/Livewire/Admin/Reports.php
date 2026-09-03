<?php

namespace App\Livewire\Admin;

use App\Models\BalanceAccount;
use App\Models\Inventory;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Services\FinanceReportService;
use Livewire\Component;

class Reports extends Component
{
    public string $type = 'sales';

    public function mount(string $type = 'sales'): void
    {
        $allowed = ['sales', 'inventory', 'payment', 'balance', 'product'];
        $this->type = in_array($type, $allowed, true) ? $type : 'sales';
    }

    public function render(FinanceReportService $reportService)
    {
        return view('livewire.admin.reports', [
            'metrics' => $reportService->getSummaryMetrics(),
            'paymentDistribution' => $reportService->getPaymentMethodDistribution(),
            'salesCount' => Sale::where('status', 'COMPLETED')->count(),
            'inventoryCount' => Inventory::count(),
            'lowStockCount' => Inventory::with('product')->get()->filter(fn ($inventory) => $inventory->stock_status !== 'AVAILABLE')->count(),
            'paymentCount' => Payment::where('status', 'COMPLETED')->count(),
            'balanceAccounts' => BalanceAccount::where('status', 'ACTIVE')->orderBy('name')->get(),
            'productCount' => Product::count(),
            'incompleteProductCount' => Product::where('price_status', 'INCOMPLETE')->count(),
        ])->layout('components.layouts.admin', ['title' => 'Laporan']);
    }
}
