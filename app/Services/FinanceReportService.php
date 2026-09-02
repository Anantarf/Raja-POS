<?php

namespace App\Services;

use App\Models\BalanceAccount;
use App\Models\Payment;
use App\Models\Sale;
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

        return [
            'omset' => $omset,
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'total_balance' => $totalBalance,
            'sales_count' => $salesCount,
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
}
