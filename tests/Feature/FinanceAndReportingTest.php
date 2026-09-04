<?php

namespace Tests\Feature;

use App\Models\BalanceAccount;
use App\Models\Location;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\BalanceService;
use App\Services\FinanceReportService;
use App\Services\InventoryService;
use App\Services\PosService;
use Database\Seeders\DatabaseSeeder;
use App\Livewire\Admin\Balances as BalancesComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinanceAndReportingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_balance_service_transfer(): void
    {
        $owner = User::where('username', 'superadmin')->first();
        $balanceService = app(BalanceService::class);

        $qris = BalanceAccount::where('code', 'QRIS')->first();
        $cash = BalanceAccount::where('code', 'CASH')->first();

        // Initial deposit into QRIS
        $balanceService->deposit($qris, 500000, 'Setor QRIS', $owner);
        $this->assertEquals(500000, $qris->fresh()->current_balance);

        // Transfer 200,000 from QRIS to CASH
        $trx = $balanceService->transfer($qris, $cash, 200000, 'Tarik tunai QRIS ke Cash', $owner);

        $this->assertNotNull($trx);
        $this->assertEquals('TRANSFER', $trx->transaction_type);
        $this->assertEquals(300000, $qris->fresh()->current_balance);
        $this->assertEquals(200000, $cash->fresh()->current_balance);
    }

    public function test_balance_service_deposit_and_withdrawal(): void
    {
        $owner = User::where('username', 'superadmin')->first();
        $balanceService = app(BalanceService::class);

        $bankBca = BalanceAccount::where('code', 'BANK_BCA')->first();

        // Deposit
        $balanceService->deposit($bankBca, 1000000, 'Setor Modal Awal', $owner);
        $this->assertEquals(1000000, $bankBca->fresh()->current_balance);

        // Withdrawal
        $balanceService->withdraw($bankBca, 300000, 'Beli Perlengkapan Toko', $owner);
        $this->assertEquals(700000, $bankBca->fresh()->current_balance);
    }

    public function test_balance_service_adjust_reconciliation(): void
    {
        $owner = User::where('username', 'superadmin')->first();
        $balanceService = app(BalanceService::class);

        $cash = BalanceAccount::where('code', 'CASH')->first();

        $balanceService->adjustBalance($cash, 150000, 'Selisih Uang Fisik Kasir', $owner);
        $this->assertEquals(150000, $cash->fresh()->current_balance);
    }

    public function test_finance_report_service_metrics(): void
    {
        $location = Location::where('code', 'RAJA-BANGO')->first();
        $owner = User::where('username', 'superadmin')->first();
        $posService = app(PosService::class);
        $reportService = app(FinanceReportService::class);
        $inventoryService = app(InventoryService::class);

        $product = Product::create([
            'code' => 'ACC-FIN-01',
            'name' => 'Kabel Type-C Braided',
            'product_type' => 'PHYSICAL',
            'cost_price' => 20000,
            'selling_price' => 50000,
        ]);

        $inventoryService->adjustStock($product, $location, 10, 'ADJUSTMENT_IN', 'Stock', $owner);

        $cashPm = PaymentMethod::where('code', 'CASH')->first();
        $cashAccount = BalanceAccount::where('code', 'CASH')->first();

        // Checkout 2 units (Total Omset 100,000, Total Cost 40,000, Profit 60,000)
        $posService->processCheckout(
            cashier: $owner,
            cartItems: [['product' => $product, 'quantity' => 2]],
            paymentsData: [['payment_method_id' => $cashPm->id, 'balance_account_id' => $cashAccount->id, 'amount' => 100000]]
        );

        $metrics = $reportService->getSummaryMetrics();

        $this->assertEquals(100000, $metrics['omset']);
        $this->assertEquals(40000, $metrics['cogs']);
        $this->assertEquals(60000, $metrics['gross_profit']);
        $this->assertEquals(1, $metrics['sales_count']);
        $this->assertEquals(100000, $metrics['total_balance']);
    }

    public function test_profit_permission_boundary(): void
    {
        $cashierRole = Role::where('name', 'CASHIER')->first();
        $ownerRole = Role::where('name', 'OWNER')->first();

        $cashier = User::create([
            'name' => 'Kasir Fin Test',
            'username' => 'kasirfin',
            'password' => bcrypt('password'),
            'role_id' => $cashierRole->id,
            'status' => 'ACTIVE',
        ]);

        $owner = User::create([
            'name' => 'Owner Fin Test',
            'username' => 'ownerfin',
            'password' => bcrypt('password'),
            'role_id' => $ownerRole->id,
            'status' => 'ACTIVE',
        ]);

        $this->assertFalse($cashier->hasPermission('report.profit.view'));
        $this->assertTrue($owner->hasPermission('report.profit.view'));
    }

    public function test_balance_component_rejects_invalid_modal_type(): void
    {
        $owner = User::where('username', 'superadmin')->first();

        Livewire::actingAs($owner)
            ->test(BalancesComponent::class)
            ->set('showModal', 'INVALID')
            ->set('amount', 10000)
            ->set('description', 'Invalid modal type')
            ->call('processTransaction')
            ->assertHasErrors(['showModal']);
    }
    public function test_balance_service_blocks_overdraw(): void
    {
        $owner = User::where('username', 'superadmin')->first();
        $cash = BalanceAccount::where('code', 'CASH')->first();

        $this->expectException(\InvalidArgumentException::class);
        app(BalanceService::class)->withdraw($cash, 1, 'Uji saldo kurang', $owner);
    }
}
