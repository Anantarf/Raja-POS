<?php

namespace App\Livewire\Admin;

use App\Services\FinanceReportService;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(FinanceReportService $reportService)
    {
        $metrics = $reportService->getSummaryMetrics();
        $dailyTrends = $reportService->getDailySalesTrend(7);

        return view('livewire.admin.dashboard', [
            'metrics' => $metrics,
            'dailyTrends' => $dailyTrends,
        ])->layout('components.layouts.admin', ['title' => 'Dashboard']);
    }
}
