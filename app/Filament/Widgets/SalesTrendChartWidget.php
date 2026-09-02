<?php

namespace App\Filament\Widgets;

use App\Services\FinanceReportService;
use Filament\Widgets\ChartWidget;

class SalesTrendChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Tren Omset Penjualan Harian (7 Hari Terakhir)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $trend = app(FinanceReportService::class)->getDailySalesTrend(7);

        return [
            'datasets' => [
                [
                    'label' => 'Omset (Rp)',
                    'data' => $trend['data'],
                    'borderColor' => '#2563EB',
                    'backgroundColor' => 'rgba(37, 99, 235, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $trend['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
