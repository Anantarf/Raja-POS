<?php

namespace App\Services;

use App\Models\BalanceAccount;
use App\Models\Inventory;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Carbon;

class FinanceReportService
{
    /**
     * Get aggregate financial metrics (Omset, COGS, Profit, Total Cash & Bank).
     */
    public function getSummaryMetrics(?string $startDate = null, ?string $endDate = null): array
    {
        $query = Sale::where('status', 'COMPLETED');

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

        $totalBalance = (float) BalanceAccount::where('status', 'ACTIVE')->sum('current_balance');

        // Calculate comparison with previous day for growth percentage
        $todayOmset = (float) Sale::where('status', 'COMPLETED')->whereDate('transaction_date', Carbon::today())->sum('total_amount');
        $yesterdayOmset = (float) Sale::where('status', 'COMPLETED')->whereDate('transaction_date', Carbon::yesterday())->sum('total_amount');

        $growth = 0.0;
        if ($yesterdayOmset > 0) {
            $growth = (($todayOmset - $yesterdayOmset) / $yesterdayOmset) * 100;
        } elseif ($todayOmset > 0) {
            $growth = 100.0;
        } else {
            $growth = 0.0;
        }

        return [
            'omset' => $omset,
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'total_balance' => $totalBalance,
            'sales_count' => $salesCount,
            'omset_growth' => round($growth, 1),
        ];
    }

    /**
     * Get breakdown of payments by payment method name.
     */
    public function getPaymentMethodDistribution(?string $startDate = null, ?string $endDate = null): array
    {
        $query = Payment::query()
            ->join('sales', 'payments.sale_id', '=', 'sales.id')
            ->join('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id')
            ->where('sales.status', 'COMPLETED');

        if ($startDate) {
            $query->whereDate('sales.transaction_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('sales.transaction_date', '<=', $endDate);
        }

        return $query->selectRaw('payment_methods.name as method_name, SUM(payments.amount) as total_amount')
            ->groupBy('payment_methods.name')
            ->pluck('total_amount', 'method_name')
            ->toArray();
    }

    /**
     * Get daily sales trend for the past N days.
     */
    public function getDailySalesTrend(int $days = 7): array
    {
        $startDate = Carbon::today()->subDays($days - 1);
        $labels = [];
        $data = [];

        for ($i = 0; $i < $days; $i++) {
            $date = (clone $startDate)->addDays($i);
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('d M');

            $dailyOmset = (float) Sale::where('status', 'COMPLETED')
                ->whereDate('transaction_date', $dateStr)
                ->sum('total_amount');

            $data[] = $dailyOmset;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get top selling products.
     */
    public function getTopSellingProducts(?string $startDate = null, ?string $endDate = null, int $limit = 5)
    {
        $query = SaleItem::with('product')
            ->whereHas('sale', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'COMPLETED');
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
            ];
        })->sortByDesc('total_qty')->take($limit)->values();
    }

    /**
     * Get cashier performance metrics.
     */
    public function getCashierPerformance(?string $startDate = null, ?string $endDate = null)
    {
        $query = Sale::with('cashier')->where('status', 'COMPLETED');

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
                'total_margin' => (float) $group->sum('gross_profit'),
            ];
        })->sortByDesc('total_omset')->values();
    }

    /**
     * Get total inventory cost valuation.
     */
    public function getInventoryValuation(): array
    {
        $inventories = Inventory::with('product')->get();
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
    public function getCategoryBreakdown(?string $startDate = null, ?string $endDate = null)
    {
        $query = SaleItem::with(['product.category'])
            ->whereHas('sale', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'COMPLETED');
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
                ];
            })->sortByDesc('total_omset')->values();
    }
}
