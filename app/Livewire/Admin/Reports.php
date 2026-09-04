<?php

namespace App\Livewire\Admin;

use App\Models\BalanceAccount;
use App\Models\Inventory;
use App\Models\Product;
use App\Services\FinanceReportService;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Reports extends Component
{
    public string $type = 'sales';

    public string $period = 'all_time'; // 'today', '7_days', 'this_month', 'all_time', 'custom'

    public ?string $startDate = null;

    public ?string $endDate = null;

    public function mount(string $type = 'sales'): void
    {
        $allowed = ['sales', 'cashier', 'inventory', 'payment', 'balance', 'product'];
        $this->type = in_array($type, $allowed, true) ? $type : 'sales';
    }

    public function updatedPeriod(): void
    {
        if ($this->period === 'today') {
            $this->startDate = Carbon::today()->toDateString();
            $this->endDate = Carbon::today()->toDateString();
        } elseif ($this->period === '7_days') {
            $this->startDate = Carbon::today()->subDays(6)->toDateString();
            $this->endDate = Carbon::today()->toDateString();
        } elseif ($this->period === 'this_month') {
            $this->startDate = Carbon::today()->startOfMonth()->toDateString();
            $this->endDate = Carbon::today()->endOfMonth()->toDateString();
        } elseif ($this->period === 'all_time') {
            $this->startDate = null;
            $this->endDate = null;
        }
    }

    public function render(FinanceReportService $reportService)
    {
        $user = auth()->user();
        $metrics = $reportService->getSummaryMetrics($this->startDate, $this->endDate, $user);
        $paymentDistribution = $reportService->getPaymentMethodDistribution($this->startDate, $this->endDate, $user);
        $topProducts = $reportService->getTopSellingProducts($this->startDate, $this->endDate, 5, $user);
        $cashierPerformance = $reportService->getCashierPerformance($this->startDate, $this->endDate, $user);
        $inventoryValuation = $reportService->getInventoryValuation($user);
        $categoryBreakdown = $reportService->getCategoryBreakdown($this->startDate, $this->endDate, $user);
        $dailyTrend = $reportService->getDailySalesTrend(7, $user);

        return view('livewire.admin.reports', [
            'metrics' => $metrics,
            'paymentDistribution' => $paymentDistribution,
            'topProducts' => $topProducts,
            'cashierPerformance' => $cashierPerformance,
            'inventoryValuation' => $inventoryValuation,
            'categoryBreakdown' => $categoryBreakdown,
            'dailyTrend' => $dailyTrend,
            'salesCount' => $metrics['sales_count'] ?? 0,
            'inventoryCount' => Inventory::forUserLocation($user)->count(),
            'lowStockCount' => Inventory::forUserLocation($user)->with('product')->get()->filter(fn ($inventory) => $inventory->stock_status !== 'AVAILABLE')->count(),
            'balanceAccounts' => BalanceAccount::forUserLocation($user)->where('status', 'ACTIVE')->orderBy('name')->get(),
            'productCount' => Product::count(),
            'incompleteProductCount' => Product::where('price_status', 'INCOMPLETE')->count(),
        ])->layout('components.layouts.admin', ['title' => 'Laporan']);
    }
}
