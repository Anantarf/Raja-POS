<?php

namespace App\Livewire\Admin;

use App\Models\BalanceAccount;
use App\Models\DailySummary;
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

    // Daily Summary specific properties
    public string $summaryDate = '';

    public $danaSaldoAwal = 0;
    public $danaTopup = 0;
    public $danaTrx = 0;
    public $danaSaldoAndroid = 0;

    public $qrisTarikTunai = 0;
    public $qrisSaldoAndroid = 0;

    public $bankmasSaldoAwal = 0;
    public $bankmasTopup = 0;
    public $bankmasTrx = 0;
    public $bankmasSaldoAndroid = 0;

    public $bcaSaldoAwal = 0;
    public $bcaTopup = 0;
    public $bcaTrx = 0;
    public $bcaSaldoAndroid = 0;

    public $multiSaldoAwal = 0;
    public $multiTopup = 0;
    public $multiTrx = 0;
    public $multiSaldoAndroid = 0;

    public $wahanaSaldoAwal = 0;
    public $wahanaTopup = 0;
    public $wahanaTrx = 0;
    public $wahanaSaldoAndroid = 0;

    public $tarikTunaiKasir = 0;
    public ?string $notes = null;

    public function mount(string $type = 'sales'): void
    {
        $allowed = ['sales', 'daily_summary', 'cashier', 'inventory', 'payment', 'balance', 'product'];
        $this->type = in_array($type, $allowed, true) ? $type : 'sales';

        $this->summaryDate = Carbon::today()->toDateString();
    }

    public function updatedSummaryDate(FinanceReportService $reportService): void
    {
        $this->loadDailySummaryForm($reportService);
    }

    public function setSummaryDateToToday(FinanceReportService $reportService): void
    {
        $this->summaryDate = Carbon::today()->toDateString();
        $this->loadDailySummaryForm($reportService);
    }

    public function setSummaryDateToYesterday(FinanceReportService $reportService): void
    {
        $this->summaryDate = Carbon::yesterday()->toDateString();
        $this->loadDailySummaryForm($reportService);
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

    public function loadDailySummaryForm(FinanceReportService $reportService): void
    {
        $user = auth()->user();
        $data = $reportService->getDailySummaryReportData($this->summaryDate, $user);

        $this->danaSaldoAwal = $data['dana_saldo_awal'];
        $this->danaTopup = $data['dana_topup'];
        $this->danaTrx = $data['dana_trx'];
        $this->danaSaldoAndroid = $data['dana_saldo_android'];

        $this->qrisTarikTunai = $data['qris_tarik_tunai'];
        $this->qrisSaldoAndroid = $data['qris_saldo_android'];

        $this->bankmasSaldoAwal = $data['bankmas_saldo_awal'];
        $this->bankmasTopup = $data['bankmas_topup'];
        $this->bankmasTrx = $data['bankmas_trx'];
        $this->bankmasSaldoAndroid = $data['bankmas_saldo_android'];

        $this->bcaSaldoAwal = $data['bca_saldo_awal'];
        $this->bcaTopup = $data['bca_topup'];
        $this->bcaTrx = $data['bca_trx'];
        $this->bcaSaldoAndroid = $data['bca_saldo_android'];

        $this->multiSaldoAwal = $data['multi_saldo_awal'];
        $this->multiTopup = $data['multi_topup'];
        $this->multiTrx = $data['multi_trx'];
        $this->multiSaldoAndroid = $data['multi_saldo_android'];

        $this->wahanaSaldoAwal = $data['wahana_saldo_awal'];
        $this->wahanaTopup = $data['wahana_topup'];
        $this->wahanaTrx = $data['wahana_trx'];
        $this->wahanaSaldoAndroid = $data['wahana_saldo_android'];

        $this->tarikTunaiKasir = $data['tarik_tunai_kasir'];
        $this->notes = $data['saved_model']?->notes;
    }

    public bool $showInputModal = false;

    public function openInputModal(FinanceReportService $reportService): void
    {
        $user = auth()->user();
        $existing = DailySummary::forUserLocation($user)->whereDate('summary_date', $this->summaryDate)->first();

        if ($existing && $existing->status === 'SUDAH_DICEK' && ! $user->can('balance.adjust') && $user->role?->name !== 'OWNER') {
            $this->dispatch('notify', message: 'Laporan tanggal ini sudah divalidasi & terkunci.', type: 'danger');
            return;
        }

        $this->loadDailySummaryForm($reportService);
        $this->showInputModal = true;
    }

    public function closeInputModal(): void
    {
        $this->showInputModal = false;
    }

    public function saveInputModal(FinanceReportService $reportService): void
    {
        $this->saveDraft($reportService);
        $this->showInputModal = false;
    }

    public function startChecking(FinanceReportService $reportService): void
    {
        $this->openInputModal($reportService);
    }

    public function saveDraft(FinanceReportService $reportService): void
    {
        $user = auth()->user();
        $locationId = $user->location_id ?? 1;

        $existing = DailySummary::forUserLocation($user)->whereDate('summary_date', $this->summaryDate)->first();
        if ($existing && $existing->status === 'SUDAH_DICEK' && ! $user->can('balance.adjust')) {
            $this->dispatch('notify', message: 'Laporan tanggal ini sudah terkunci dan tidak dapat diubah.', type: 'danger');
            return;
        }

        $data = $reportService->getDailySummaryReportData($this->summaryDate, $user);
        $hasDiscrepancy = $data['has_discrepancy'];

        DailySummary::updateOrCreate(
            [
                'location_id' => $locationId,
                'summary_date' => $this->summaryDate,
            ],
            [
                'status' => 'SEDANG_DICEK',
                'has_discrepancy' => $hasDiscrepancy,
                'dana_saldo_awal' => (float) $this->danaSaldoAwal,
                'dana_topup' => (float) $this->danaTopup,
                'dana_trx' => (float) $this->danaTrx,
                'dana_saldo_android' => (float) $this->danaSaldoAndroid,
                'qris_tarik_tunai' => (float) $this->qrisTarikTunai,
                'qris_saldo_android' => (float) $this->qrisSaldoAndroid,
                'bankmas_saldo_awal' => (float) $this->bankmasSaldoAwal,
                'bankmas_topup' => (float) $this->bankmasTopup,
                'bankmas_trx' => (float) $this->bankmasTrx,
                'bankmas_saldo_android' => (float) $this->bankmasSaldoAndroid,
                'bca_saldo_awal' => (float) $this->bcaSaldoAwal,
                'bca_topup' => (float) $this->bcaTopup,
                'bca_trx' => (float) $this->bcaTrx,
                'bca_saldo_android' => (float) $this->bcaSaldoAndroid,
                'multi_saldo_awal' => (float) $this->multiSaldoAwal,
                'multi_topup' => (float) $this->multiTopup,
                'multi_trx' => (float) $this->multiTrx,
                'multi_saldo_android' => (float) $this->multiSaldoAndroid,
                'wahana_saldo_awal' => (float) $this->wahanaSaldoAwal,
                'wahana_topup' => (float) $this->wahanaTopup,
                'wahana_trx' => (float) $this->wahanaTrx,
                'wahana_saldo_android' => (float) $this->wahanaSaldoAndroid,
                'tarik_tunai_kasir' => (float) $this->tarikTunaiKasir,
                'notes' => $this->notes,
                'created_by' => $user->id,
            ]
        );

        $this->dispatch('notify', message: 'Draf Rekap Harian berhasil disimpan (Status: Sedang Di-Cek).', type: 'success');
    }

    public function validateAndLock(FinanceReportService $reportService): void
    {
        $user = auth()->user();
        $locationId = $user->location_id ?? 1;

        $data = $reportService->getDailySummaryReportData($this->summaryDate, $user);

        $danaAkhir = (float) $this->danaSaldoAwal + (float) $this->danaTopup - (float) $this->danaTrx;
        $danaSelisih = (float) $this->danaSaldoAndroid - $danaAkhir;

        $qrisExpected = (float) ($data['qris_pembayaran'] ?? 0) + (float) $this->qrisTarikTunai;
        $qrisSelisih = (float) $this->qrisSaldoAndroid - $qrisExpected;

        $bankmasAkhir = (float) $this->bankmasSaldoAwal + (float) $this->bankmasTopup - (float) $this->bankmasTrx;
        $bankmasSelisih = (float) $this->bankmasSaldoAndroid - $bankmasAkhir;

        $bcaAkhir = (float) $this->bcaSaldoAwal + (float) $this->bcaTopup - (float) $this->bcaTrx;
        $bcaSelisih = (float) $this->bcaSaldoAndroid - $bcaAkhir;

        $multiAkhir = (float) $this->multiSaldoAwal + (float) $this->multiTopup - (float) $this->multiTrx;
        $multiSelisih = (float) $this->multiSaldoAndroid - $multiAkhir;

        $wahanaAkhir = (float) $this->wahanaSaldoAwal + (float) $this->wahanaTopup - (float) $this->wahanaTrx;
        $wahanaSelisih = (float) $this->wahanaSaldoAndroid - $wahanaAkhir;

        $hasDiscrepancy = ($danaSelisih != 0 || $qrisSelisih != 0 || $bankmasSelisih != 0 || $bcaSelisih != 0 || $multiSelisih != 0 || $wahanaSelisih != 0);

        if ($hasDiscrepancy && empty(trim($this->notes ?? ''))) {
            $this->dispatch('notify', message: 'Terdapat selisih pada saldo. Harap cantumkan penjelasan selisih di kolom catatan sebelum memvalidasi.', type: 'amber');
            return;
        }

        DailySummary::updateOrCreate(
            [
                'location_id' => $locationId,
                'summary_date' => $this->summaryDate,
            ],
            [
                'status' => 'SUDAH_DICEK',
                'has_discrepancy' => $hasDiscrepancy,
                'dana_saldo_awal' => (float) $this->danaSaldoAwal,
                'dana_topup' => (float) $this->danaTopup,
                'dana_trx' => (float) $this->danaTrx,
                'dana_saldo_android' => (float) $this->danaSaldoAndroid,
                'qris_tarik_tunai' => (float) $this->qrisTarikTunai,
                'qris_saldo_android' => (float) $this->qrisSaldoAndroid,
                'bankmas_saldo_awal' => (float) $this->bankmasSaldoAwal,
                'bankmas_topup' => (float) $this->bankmasTopup,
                'bankmas_trx' => (float) $this->bankmasTrx,
                'bankmas_saldo_android' => (float) $this->bankmasSaldoAndroid,
                'bca_saldo_awal' => (float) $this->bcaSaldoAwal,
                'bca_topup' => (float) $this->bcaTopup,
                'bca_trx' => (float) $this->bcaTrx,
                'bca_saldo_android' => (float) $this->bcaSaldoAndroid,
                'multi_saldo_awal' => (float) $this->multiSaldoAwal,
                'multi_topup' => (float) $this->multiTopup,
                'multi_trx' => (float) $this->multiTrx,
                'multi_saldo_android' => (float) $this->multiSaldoAndroid,
                'wahana_saldo_awal' => (float) $this->wahanaSaldoAwal,
                'wahana_topup' => (float) $this->wahanaTopup,
                'wahana_trx' => (float) $this->wahanaTrx,
                'wahana_saldo_android' => (float) $this->wahanaSaldoAndroid,
                'tarik_tunai_kasir' => (float) $this->tarikTunaiKasir,
                'notes' => $this->notes,
                'created_by' => $user->id,
                'verified_at' => now(),
                'verified_by' => $user->id,
            ]
        );

        $this->showInputModal = false;

        $msg = $hasDiscrepancy ? 'Laporan berhasil divalidasi dan dikunci (Catatan Selisih Terekam).' : 'Laporan berhasil divalidasi & dikunci. Seluruh saldo sesuai!';
        $this->dispatch('notify', message: $msg, type: $hasDiscrepancy ? 'amber' : 'success');
    }

    public function unlockReport(): void
    {
        $user = auth()->user();
        abort_unless($user->can('balance.adjust') || $user->role?->name === 'OWNER', 403);

        $record = DailySummary::forUserLocation($user)->whereDate('summary_date', $this->summaryDate)->first();
        if ($record) {
            $record->update(['status' => 'SEDANG_DICEK']);
            $this->dispatch('notify', message: 'Kunci laporan dibuka. Laporan kini dapat direvisi.', type: 'info');
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
        $dailySummaryData = $reportService->getDailySummaryReportData($this->summaryDate ?: Carbon::today()->toDateString(), $user);

        return view('livewire.admin.reports', [
            'metrics' => $metrics,
            'paymentDistribution' => $paymentDistribution,
            'topProducts' => $topProducts,
            'cashierPerformance' => $cashierPerformance,
            'inventoryValuation' => $inventoryValuation,
            'categoryBreakdown' => $categoryBreakdown,
            'dailyTrend' => $dailyTrend,
            'dailySummaryData' => $dailySummaryData,
            'salesCount' => $metrics['sales_count'] ?? 0,
            'inventoryCount' => Inventory::forUserLocation($user)->count(),
            'lowStockCount' => Inventory::forUserLocation($user)->with('product')->get()->filter(fn ($inventory) => $inventory->stock_status !== 'AVAILABLE')->count(),
            'balanceAccounts' => BalanceAccount::forUserLocation($user)->where('status', 'ACTIVE')->orderBy('name')->get(),
            'productCount' => Product::count(),
            'incompleteProductCount' => Product::where('price_status', 'INCOMPLETE')->count(),
        ])->layout('components.layouts.admin', ['title' => 'Laporan Toko']);
    }
}
