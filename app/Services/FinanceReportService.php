<?php

namespace App\Services;

use App\Models\BalanceAccount;
use App\Models\DailySummary;
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
        if ($todayOmzet > 0 && $yesterdayOmzet > 0) {
            $growth = (($todayOmzet - $yesterdayOmzet) / $yesterdayOmzet) * 100;
        } elseif ($todayOmzet > 0) {
            $growth = 100.0;
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

        $totalsByDate = Sale::forUserLocation($user)
            ->where('status', 'COMPLETED')
            ->whereBetween('transaction_date', [$startDate->copy()->startOfDay(), Carbon::today()->endOfDay()])
            ->selectRaw('DATE(transaction_date) as sale_date, SUM(total_amount) as total_amount')
            ->groupByRaw('DATE(transaction_date)')
            ->pluck('total_amount', 'sale_date');

        for ($i = 0; $i < $days; $i++) {
            $date = (clone $startDate)->addDays($i);
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('d M');
            $data[] = (float) ($totalsByDate[$dateStr] ?? 0);
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

        return SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->leftJoin('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.status', 'COMPLETED')
            ->when($user && ! $user->hasGlobalLocationAccess(), fn ($query) => $user->location_id ? $query->where('sales.location_id', $user->location_id) : $query->whereRaw('1 = 0'))
            ->when($startDate, fn ($query) => $query->whereDate('sales.transaction_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('sales.transaction_date', '<=', $endDate))
            ->selectRaw("COALESCE(products.name, sale_items.product_name_snapshot, 'Produk Dihapus') as product_name")
            ->selectRaw("COALESCE(products.code, sale_items.product_code_snapshot, '-') as code")
            ->selectRaw('SUM(sale_items.quantity) as total_qty, SUM(sale_items.subtotal) as total_omset, SUM(sale_items.subtotal) as total_omzet')
            ->groupBy('sale_items.product_id', 'products.name', 'products.code', 'sale_items.product_name_snapshot', 'sale_items.product_code_snapshot')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();
    }

    /**
     * Get cashier performance metrics.
     */
    public function getCashierPerformance(?string $startDate = null, ?string $endDate = null, ?User $user = null)
    {
        $user = $user ?? auth()->user();

        return Sale::forUserLocation($user)
            ->leftJoin('users', 'sales.cashier_id', '=', 'users.id')
            ->where('sales.status', 'COMPLETED')
            ->when($startDate, fn ($query) => $query->whereDate('sales.transaction_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('sales.transaction_date', '<=', $endDate))
            ->selectRaw("COALESCE(users.name, 'System') as cashier_name")
            ->selectRaw('COUNT(sales.id) as total_sales, SUM(sales.total_amount) as total_omset, SUM(sales.total_amount) as total_omzet, SUM(sales.gross_profit) as total_margin')
            ->groupBy('sales.cashier_id', 'users.name')
            ->orderByDesc('total_omzet')
            ->get();
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

        return SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->leftJoin('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('sales.status', 'COMPLETED')
            ->when($user && ! $user->hasGlobalLocationAccess(), fn ($query) => $user->location_id ? $query->where('sales.location_id', $user->location_id) : $query->whereRaw('1 = 0'))
            ->when($startDate, fn ($query) => $query->whereDate('sales.transaction_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('sales.transaction_date', '<=', $endDate))
            ->selectRaw("COALESCE(categories.name, 'Tanpa Kategori') as category_name")
            ->selectRaw('SUM(sale_items.quantity) as total_qty, SUM(sale_items.subtotal) as total_omset, SUM(sale_items.subtotal) as total_omzet')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_omzet')
            ->get();
    }

    /**
     * Get detailed daily summary reconciliation report for a specific date.
     */
    public function getDailySummaryReportData(?string $date = null, ?User $user = null): array
    {
        $user = $user ?? auth()->user();
        $targetDate = $date ?: Carbon::today()->toDateString();

        // 1. Sales & Profit from POS
        $salesQuery = Sale::forUserLocation($user)
            ->where('status', 'COMPLETED')
            ->whereDate('transaction_date', $targetDate);

        $totalPenjualan = (float) $salesQuery->sum('total_amount');
        $totalCost = (float) $salesQuery->sum('total_cost');
        $margin = $totalPenjualan - $totalCost;

        // 2. Payment Method distribution for targetDate
        $payments = Payment::query()
            ->whereHas('sale', function ($sq) use ($targetDate, $user) {
                $sq->forUserLocation($user)->where('status', 'COMPLETED')->whereDate('transaction_date', $targetDate);
            })
            ->with('paymentMethod')
            ->get();

        $qrisPembayaran = 0.0;
        $tunaiPembayaran = 0.0;
        $transferPembayaran = 0.0;

        foreach ($payments as $payment) {
            $type = strtoupper($payment->paymentMethod?->type ?? '');
            $code = strtoupper($payment->paymentMethod?->code ?? '');
            $amount = (float) $payment->amount;

            if ($type === 'QRIS' || str_contains($code, 'QRIS')) {
                $qrisPembayaran += $amount;
            } elseif ($type === 'CASH' || str_contains($code, 'CASH')) {
                $tunaiPembayaran += $amount;
            } else {
                $transferPembayaran += $amount;
            }
        }

        // 3. Saved Daily Summary snapshot from database
        $savedSummary = DailySummary::forUserLocation($user)
            ->whereDate('summary_date', $targetDate)
            ->with(['verifier', 'creator'])
            ->first();

        // Defaults or saved values (inherit yesterday's closing balance if unstarted)
        if (! $savedSummary) {
            $yesterdayDate = Carbon::parse($targetDate)->subDay()->toDateString();
            $yesterdaySummary = DailySummary::forUserLocation($user)
                ->whereDate('summary_date', $yesterdayDate)
                ->first();

            $danaAwal = (float) ($yesterdaySummary?->dana_saldo_android ?? 0);
            $bankmasAwal = (float) ($yesterdaySummary?->bankmas_saldo_android ?? 0);
            $bcaAwal = (float) ($yesterdaySummary?->bca_saldo_android ?? 0);
            $multiAwal = (float) ($yesterdaySummary?->multi_saldo_android ?? 0);
            $wahanaAwal = (float) ($yesterdaySummary?->wahana_saldo_android ?? 0);
        } else {
            $danaAwal = (float) $savedSummary->dana_saldo_awal;
            $bankmasAwal = (float) $savedSummary->bankmas_saldo_awal;
            $bcaAwal = (float) ($savedSummary->bca_saldo_awal ?? 0);
            $multiAwal = (float) $savedSummary->multi_saldo_awal;
            $wahanaAwal = (float) ($savedSummary->wahana_saldo_awal ?? 0);
        }

        $danaTopup = (float) ($savedSummary?->dana_topup ?? 0);
        $danaTrx = (float) ($savedSummary?->dana_trx ?? 0);
        $danaAndroid = (float) ($savedSummary?->dana_saldo_android ?? ($danaAwal + $danaTopup - $danaTrx));

        $qrisTarikTunai = (float) ($savedSummary?->qris_tarik_tunai ?? 0);
        $qrisAndroid = (float) ($savedSummary?->qris_saldo_android ?? ($qrisPembayaran + $qrisTarikTunai));

        $bankmasTopup = (float) ($savedSummary?->bankmas_topup ?? 0);
        $bankmasTrx = (float) ($savedSummary?->bankmas_trx ?? 0);
        $bankmasAndroid = (float) ($savedSummary?->bankmas_saldo_android ?? ($bankmasAwal + $bankmasTopup - $bankmasTrx));

        $bcaTopup = (float) ($savedSummary?->bca_topup ?? 0);
        $bcaTrx = (float) ($savedSummary?->bca_trx ?? 0);
        $bcaAndroid = (float) ($savedSummary?->bca_saldo_android ?? ($bcaAwal + $bcaTopup - $bcaTrx));

        $multiTopup = (float) ($savedSummary?->multi_topup ?? 0);
        $multiTrx = (float) ($savedSummary?->multi_trx ?? 0);
        $multiAndroid = (float) ($savedSummary?->multi_saldo_android ?? ($multiAwal + $multiTopup - $multiTrx));

        $wahanaTopup = (float) ($savedSummary?->wahana_topup ?? 0);
        $wahanaTrx = (float) ($savedSummary?->wahana_trx ?? 0);
        $wahanaAndroid = (float) ($savedSummary?->wahana_saldo_android ?? ($wahanaAwal + $wahanaTopup - $wahanaTrx));

        $tarikTunaiKasir = (float) ($savedSummary?->tarik_tunai_kasir ?? $qrisTarikTunai);

        // Subtotal Netto = Penjualan - Tarik Tunai
        $subtotalNetto = $totalPenjualan - $tarikTunaiKasir;

        // Setoran Tunai (Physical cash in drawer) = (Penjualan Netto - QRIS - Transfer) + Tarik Tunai
        $setoranTunai = max(0, ($subtotalNetto - $qrisPembayaran - $transferPembayaran) + $tarikTunaiKasir);

        $danaSaldoAkhir = $danaAwal + $danaTopup - $danaTrx;
        $danaSelisih = $danaAndroid - $danaSaldoAkhir;

        $qrisTotal = $qrisPembayaran + $qrisTarikTunai;
        $qrisSelisih = $qrisAndroid - $qrisTotal;

        $bankmasSaldoAkhir = $bankmasAwal + $bankmasTopup - $bankmasTrx;
        $bankmasSelisih = $bankmasAndroid - $bankmasSaldoAkhir;

        $bcaSaldoAkhir = $bcaAwal + $bcaTopup - $bcaTrx;
        $bcaSelisih = $bcaAndroid - $bcaSaldoAkhir;

        $multiSaldoAkhir = $multiAwal + $multiTopup - $multiTrx;
        $multiSelisih = $multiAndroid - $multiSaldoAkhir;

        $wahanaSaldoAkhir = $wahanaAwal + $wahanaTopup - $wahanaTrx;
        $wahanaSelisih = $wahanaAndroid - $wahanaSaldoAkhir;

        $status = $savedSummary?->status ?? 'BELUM_DICEK';
        $hasDiscrepancy = $savedSummary ? $savedSummary->has_discrepancy : (($danaSelisih != 0) || ($qrisSelisih != 0) || ($bankmasSelisih != 0) || ($bcaSelisih != 0) || ($multiSelisih != 0) || ($wahanaSelisih != 0));

        return [
            'summary_date' => $targetDate,
            'is_saved' => $savedSummary !== null,
            'saved_model' => $savedSummary,
            'status' => $status,
            'has_discrepancy' => $hasDiscrepancy,
            'verified_at' => $savedSummary?->verified_at,
            'verifier_name' => $savedSummary?->verifier?->name,
            'creator_name' => $savedSummary?->creator?->name,

            // DANA
            'dana_saldo_awal' => $danaAwal,
            'dana_topup' => $danaTopup,
            'dana_total' => $danaAwal + $danaTopup,
            'dana_trx' => $danaTrx,
            'dana_saldo_akhir' => $danaSaldoAkhir,
            'dana_saldo_android' => $danaAndroid,
            'dana_selisih' => $danaSelisih,

            // QRIS
            'qris_pembayaran' => $qrisPembayaran,
            'qris_tarik_tunai' => $qrisTarikTunai,
            'qris_total' => $qrisTotal,
            'qris_saldo_android' => $qrisAndroid,
            'qris_cek' => $qrisAndroid,
            'qris_selisih' => $qrisSelisih,

            // BANK MAS
            'bankmas_saldo_awal' => $bankmasAwal,
            'bankmas_topup' => $bankmasTopup,
            'bankmas_total' => $bankmasAwal + $bankmasTopup,
            'bankmas_trx' => $bankmasTrx,
            'bankmas_sisa' => $bankmasSaldoAkhir,
            'bankmas_saldo_android' => $bankmasAndroid,
            'bankmas_selisih' => $bankmasSelisih,

            // BANK BCA
            'bca_saldo_awal' => $bcaAwal,
            'bca_topup' => $bcaTopup,
            'bca_total' => $bcaAwal + $bcaTopup,
            'bca_trx' => $bcaTrx,
            'bca_sisa' => $bcaSaldoAkhir,
            'bca_saldo_android' => $bcaAndroid,
            'bca_selisih' => $bcaSelisih,

            // MULTI
            'multi_saldo_awal' => $multiAwal,
            'multi_topup' => $multiTopup,
            'multi_total' => $multiAwal + $multiTopup,
            'multi_trx' => $multiTrx,
            'multi_sisa' => $multiSaldoAkhir,
            'multi_saldo_android' => $multiAndroid,
            'multi_selisih' => $multiSelisih,

            // WAHANA
            'wahana_saldo_awal' => $wahanaAwal,
            'wahana_topup' => $wahanaTopup,
            'wahana_total' => $wahanaAwal + $wahanaTopup,
            'wahana_trx' => $wahanaTrx,
            'wahana_sisa' => $wahanaSaldoAkhir,
            'wahana_saldo_android' => $wahanaAndroid,
            'wahana_selisih' => $wahanaSelisih,

            // Rekap Penjualan & Margin (Kanan)
            'margin' => $margin,
            'total_penjualan' => $totalPenjualan,
            'tarik_tunai_kasir' => $tarikTunaiKasir,
            'subtotal_netto' => $subtotalNetto,
            'qris_pembayaran_pos' => $qrisPembayaran,
            'tunai_pembayaran_pos' => $tunaiPembayaran,
            'transfer_pembayaran_pos' => $transferPembayaran,
            'setoran_tunai' => $setoranTunai,
        ];
    }
}
