<?php

namespace App\Services;

use App\Models\BalanceAccount;
use App\Models\Inventory;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Support\Carbon;

class FinanceReportService
{
    /**
     * Get aggregate financial metrics (Omzet, COGS, Profit, Total Cash & Bank).
     */
    public function getSummaryMetrics(?string $startDate = null, ?string $endDate = null, ?User $user = null): array
    {
        $user = $user ?? auth()->user();
        $query = Sale::forUserLocation($user)->where('status', 'COMPLETED');

        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }

        $omset = (float) $query->sum('total_amount');
        $cogs = (float) $query->sum('total_cost');
        $grossProfit = $omset - $cogs;
        $salesCount = $query->count();

        $totalBalance = (float) BalanceAccount::forUserLocation($user)->where('status', 'ACTIVE')->sum('current_balance');

        // Calculate comparison with previous day for growth percentage
        $todayOmzet = (float) Sale::forUserLocation($user)->where('status', 'COMPLETED')->whereDate('transaction_date', Carbon::today())->sum('total_amount');
        $yesterdayOmzet = (float) Sale::forUserLocation($user)->where('status', 'COMPLETED')->whereDate('transaction_date', Carbon::yesterday())->sum('total_amount');

        $growth = 0.0;
        if ($yesterdayOmzet > 0) {
            $growth = (($todayOmzet - $yesterdayOmzet) / $yesterdayOmzet) * 100;
        } elseif ($todayOmzet > 0) {
            $growth = 100.0;
        } else {
            $growth = 0.0;
        }

        return [
            'omset' => $omset,
            'omzet' => $omset,
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'total_balance' => $totalBalance,
            'sales_count' => $salesCount,
            'omset_growth' => round($growth, 1),
            'omzet_growth' => round($growth, 1),
        ];
    }

    /**
     * Get breakdown of payments by payment method name.
     */
    public function getPaymentMethodDistribution(?string $startDate = null, ?string $endDate = null, ?User $user = null): array
    {
        $user = $user ?? auth()->user();
        $query = Payment::query()
            ->whereHas('sale', function ($sq) use ($startDate, $endDate, $user) {
                $sq->forUserLocation($user)->where('status', 'COMPLETED');
                if ($startDate) {
                    $sq->whereDate('transaction_date', '>=', $startDate);
                }
                if ($endDate) {
                    $sq->whereDate('transaction_date', '<=', $endDate);
                }
            })
            ->join('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id');

        return $query->selectRaw('payment_methods.name as method_name, SUM(payments.amount) as total_amount')
            ->groupBy('payment_methods.name')
            ->pluck('total_amount', 'method_name')
            ->toArray();
    }

    /**
     * Get daily sales trend for the past N days.
     */
    public function getDailySalesTrend(int $days = 7, ?User $user = null): array
    {
        $user = $user ?? auth()->user();
        $startDate = Carbon::today()->subDays($days - 1);
        $labels = [];
        $data = [];

        for ($i = 0; $i < $days; $i++) {
            $date = (clone $startDate)->addDays($i);
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('d M');

            $dailyOmzet = (float) Sale::forUserLocation($user)->where('status', 'COMPLETED')
                ->whereDate('transaction_date', $dateStr)
                ->sum('total_amount');

            $data[] = $dailyOmzet;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get top selling products.
     */
    public function getTopSellingProducts(?string $startDate = null, ?string $endDate = null, int $limit = 5, ?User $user = null)
    {
        $user = $user ?? auth()->user();
        $query = SaleItem::with('product')
            ->whereHas('sale', function ($q) use ($startDate, $endDate, $user) {
                $q->forUserLocation($user)->where('status', 'COMPLETED');
                if ($startDate) {
                    $q->whereDate('transaction_date', '>=', $startDate);
                }
                if ($endDate) {
                    $q->whereDate('transaction_date', '<=', $endDate);
                }
            });

        $items = $query->get();

        return $items->groupBy('product_id')->map(function ($group) {
            $product = $group->first()->product;

            return (object) [
                'product_name' => $product?->name ?? 'Produk Dihapus',
                'code' => $product?->code ?? '-',
                'total_qty' => (int) $group->sum('quantity'),
                'total_omset' => (float) $group->sum('subtotal'),
                'total_omzet' => (float) $group->sum('subtotal'),
            ];
        })->sortByDesc('total_qty')->take($limit)->values();
    }

    /**
     * Get cashier performance metrics.
     */
    public function getCashierPerformance(?string $startDate = null, ?string $endDate = null, ?User $user = null)
    {
        $user = $user ?? auth()->user();
        $query = Sale::forUserLocation($user)->with('cashier')->where('status', 'COMPLETED');

        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }

        $sales = $query->get();

        return $sales->groupBy('cashier_id')->map(function ($group) {
            $cashierName = $group->first()->cashier?->name ?? 'System';

            return (object) [
                'cashier_name' => $cashierName,
                'total_sales' => $group->count(),
                'total_omset' => (float) $group->sum('total_amount'),
                'total_omzet' => (float) $group->sum('total_amount'),
                'total_margin' => (float) $group->sum('gross_profit'),
            ];
        })->sortByDesc('total_omzet')->values();
    }

    /**
     * Get total inventory cost valuation.
     */
    public function getInventoryValuation(?User $user = null): array
    {
        $user = $user ?? auth()->user();
        $inventories = Inventory::forUserLocation($user)->with('product')->get();
        $totalCostValuation = 0.0;
        $totalRetailValuation = 0.0;
        $totalStockUnits = 0;

        foreach ($inventories as $inv) {
            $cost = (float) ($inv->product->cost_price ?? 0);
            $price = (float) ($inv->product->selling_price ?? 0);
            $qty = (int) ($inv->quantity ?? 0);

            $totalCostValuation += ($cost * $qty);
            $totalRetailValuation += ($price * $qty);
            $totalStockUnits += $qty;
        }

        return [
            'total_cost' => $totalCostValuation,
            'total_retail' => $totalRetailValuation,
            'total_units' => $totalStockUnits,
        ];
    }

    /**
     * Get sales breakdown by product category.
     */
    public function getCategoryBreakdown(?string $startDate = null, ?string $endDate = null, ?User $user = null)
    {
        $user = $user ?? auth()->user();
        $query = SaleItem::with(['product.category'])
            ->whereHas('sale', function ($q) use ($startDate, $endDate, $user) {
                $q->forUserLocation($user)->where('status', 'COMPLETED');
                if ($startDate) {
                    $q->whereDate('transaction_date', '>=', $startDate);
                }
                if ($endDate) {
                    $q->whereDate('transaction_date', '<=', $endDate);
                }
            });

        $items = $query->get();

        return $items->groupBy(fn ($item) => $item->product?->category?->name ?? 'Tanpa Kategori')
            ->map(function ($group, $categoryName) {
                return (object) [
                    'category_name' => $categoryName,
                    'total_qty' => (int) $group->sum('quantity'),
                    'total_omset' => (float) $group->sum('subtotal'),
                    'total_omzet' => (float) $group->sum('subtotal'),
                ];
            })->sortByDesc('total_omzet')->values();
    }
}
