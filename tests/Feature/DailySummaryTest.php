<?php

namespace Tests\Feature;

use App\Models\DailySummary;
use App\Models\User;
use App\Services\FinanceReportService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class DailySummaryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_daily_summary_status_workflow_draft_to_locked()
    {
        $user = User::whereHas('role', fn ($q) => $q->where('name', 'OWNER'))->first();
        $this->actingAs($user);

        $today = Carbon::today()->toDateString();

        // 1. Save Draft
        Livewire::test(\App\Livewire\Admin\Reports::class, ['type' => 'daily_summary'])
            ->set('summaryDate', $today)
            ->set('danaSaldoAwal', 783078)
            ->set('danaTopup', 5221000)
            ->set('danaTrx', 5035000)
            ->set('danaSaldoAndroid', 969078)
            ->set('qrisTarikTunai', 22000)
            ->set('qrisSaldoAndroid', 22000)
            ->set('bankmasSaldoAwal', 2683445)
            ->set('bankmasTopup', 6142500)
            ->set('bankmasTrx', 7887500)
            ->set('bankmasSaldoAndroid', 938445)
            ->set('multiSaldoAwal', 966832)
            ->set('multiTrx', 342454)
            ->set('multiSaldoAndroid', 624378)
            ->call('saveDraft')
            ->assertDispatched('notify');

        $this->assertDatabaseHas('daily_summaries', [
            'location_id' => $user->location_id,
            'status' => 'SEDANG_DICEK',
            'dana_saldo_awal' => 783078,
            'dana_saldo_android' => 969078,
        ]);

        // 2. Validate & Lock
        Livewire::test(\App\Livewire\Admin\Reports::class, ['type' => 'daily_summary'])
            ->set('summaryDate', $today)
            ->set('danaSaldoAwal', 783078)
            ->set('danaTopup', 5221000)
            ->set('danaTrx', 5035000)
            ->set('danaSaldoAndroid', 969078)
            ->set('qrisTarikTunai', 22000)
            ->set('qrisSaldoAndroid', 22000)
            ->call('validateAndLock')
            ->assertDispatched('notify');

        $this->assertDatabaseHas('daily_summaries', [
            'location_id' => $user->location_id,
            'status' => 'SUDAH_DICEK',
            'verified_by' => $user->id,
        ]);

        $service = app(FinanceReportService::class);
        $data = $service->getDailySummaryReportData($today, $user);

        $this->assertEquals('SUDAH_DICEK', $data['status']);
        $this->assertEquals($user->name, $data['verifier_name']);

        // 3. Unlock Report by Owner
        Livewire::test(\App\Livewire\Admin\Reports::class, ['type' => 'daily_summary'])
            ->set('summaryDate', $today)
            ->call('unlockReport')
            ->assertDispatched('notify');

        $this->assertDatabaseHas('daily_summaries', [
            'location_id' => $user->location_id,
            'status' => 'SEDANG_DICEK',
        ]);
    }
}
