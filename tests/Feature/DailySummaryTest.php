<?php

namespace Tests\Feature;

use App\Services\FinanceReportService;
use App\Models\User;
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

    public function test_can_save_and_retrieve_daily_summary()
    {
        $user = User::whereHas('role', fn ($q) => $q->where('name', 'OWNER'))->first();
        $this->actingAs($user);

        $today = Carbon::today()->toDateString();

        Livewire::test(\App\Livewire\Admin\Reports::class, ['type' => 'daily_summary'])
            ->set('summaryDate', $today)
            ->set('danaSaldoAwal', 783078)
            ->set('danaTopup', 5221000)
            ->set('danaTrx', 5035000)
            ->set('danaSaldoAndroid', 969078)
            ->set('qrisTarikTunai', 22000)
            ->set('bankmasSaldoAwal', 2683445)
            ->set('bankmasTopup', 6142500)
            ->set('bankmasTrx', 7887500)
            ->set('bankmasSaldoAndroid', 938445)
            ->set('multiSaldoAwal', 966832)
            ->set('multiTrx', 342454)
            ->set('multiSaldoAndroid', 624378)
            ->call('saveDailySummary')
            ->assertDispatched('notify');

        $this->assertDatabaseHas('daily_summaries', [
            'location_id' => $user->location_id,
            'dana_saldo_awal' => 783078,
            'dana_topup' => 5221000,
            'dana_trx' => 5035000,
            'dana_saldo_android' => 969078,
            'qris_tarik_tunai' => 22000,
            'bankmas_saldo_awal' => 2683445,
            'bankmas_topup' => 6142500,
            'bankmas_trx' => 7887500,
            'bankmas_saldo_android' => 938445,
            'multi_saldo_awal' => 966832,
            'multi_trx' => 342454,
            'multi_saldo_android' => 624378,
        ]);

        $service = app(FinanceReportService::class);
        $data = $service->getDailySummaryReportData($today, $user);

        $this->assertTrue($data['is_saved']);
        $this->assertEquals(783078, $data['dana_saldo_awal']);
        $this->assertEquals(969078, $data['dana_saldo_akhir']);
        $this->assertEquals(969078, $data['dana_saldo_android']);
    }
}
