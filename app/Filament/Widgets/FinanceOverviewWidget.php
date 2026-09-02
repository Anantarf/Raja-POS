<?php

namespace App\Filament\Widgets;

use App\Services\FinanceReportService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinanceOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $reportService = app(FinanceReportService::class);
        $metrics = $reportService->getSummaryMetrics();

        $canViewProfit = auth()->user()?->hasPermission('report.profit.view') ?? false;

        $stats = [
            Stat::make('Total Omset Penjualan', 'Rp ' . number_format($metrics['omset'], 0, ',', '.'))
                ->description('Total transaksi COMPLETED')
                ->color('success'),

            Stat::make('Total Akumulasi Saldo Kas & Bank', 'Rp ' . number_format($metrics['total_balance'], 0, ',', '.'))
                ->description('Saldo riil seluruh akun aktif')
                ->color('info'),

            Stat::make('Jumlah Transaksi Sukses', $metrics['sales_count'] . ' Transaksi')
                ->description('Transaksi berhasil')
                ->color('primary'),
        ];

        if ($canViewProfit) {
            array_splice($stats, 1, 0, [
                Stat::make('Laba Kotor (Gross Profit)', 'Rp ' . number_format($metrics['gross_profit'], 0, ',', '.'))
                    ->description('Omset dikurangi HPP/Modal')
                    ->color('warning'),
            ]);
        }

        return $stats;
    }
}
