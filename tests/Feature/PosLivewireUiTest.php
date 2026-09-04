<?php

namespace Tests\Feature;

use App\Jobs\PurgeExpiredTrashedSalesJob;
use App\Livewire\Pos\Checkout;
use App\Models\AuditLog;
use App\Models\BalanceAccount;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use App\Services\PosService;
use App\Services\SaleCancellationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PosLivewireUiTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $cashier;
    protected Location $locationBango;
    protected Location $locationDuren;
    protected Product $productPhysical;
    protected PaymentMethod $cashMethod;
    protected BalanceAccount $cashAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $ownerRole = Role::create(['name' => 'OWNER', 'display_name' => 'Owner']);
        $cashierRole = Role::create(['name' => 'CASHIER', 'display_name' => 'Cashier']);

        $trashPerm = \App\Models\Permission::create(['name' => 'sales.trash', 'display_name' => 'Cancel Sale']);
        $restorePerm = \App\Models\Permission::create(['name' => 'sales.restore', 'display_name' => 'Restore Sale']);
        $cashierRole->permissions()->attach([$trashPerm->id, $restorePerm->id]);

        $this->locationBango = Location::create(['name' => 'Raja Bango', 'code' => 'RAJA-BANGO', 'status' => 'ACTIVE']);
        $this->locationDuren = Location::create(['name' => 'Raja Duren', 'code' => 'RAJA-DUREN', 'status' => 'ACTIVE']);

        $this->owner = User::create([
            'name' => 'Owner Boss',
            'username' => 'ownerboss',
            'email' => 'owner@raja.com',
            'password' => bcrypt('password'),
            'role_id' => $ownerRole->id,
            'location_id' => $this->locationBango->id,
            'status' => 'ACTIVE',
        ]);

        $this->cashier = User::create([
            'name' => 'Kasir Bango',
            'username' => 'kasirbango',
            'email' => 'kasir@raja.com',
            'password' => bcrypt('password'),
            'role_id' => $cashierRole->id,
            'location_id' => $this->locationBango->id,
            'status' => 'ACTIVE',
        ]);

        $this->productPhysical = Product::create([
            'code' => 'PRD-ACC-001',
            'name' => 'Kabel Data Type C',
            'slug' => 'kabel-data-type-c',
            'cost_price' => 10000,
            'selling_price' => 25000,
            'price_status' => 'COMPLETE',
            'product_type' => 'PHYSICAL',
            'status' => 'ACTIVE',
        ]);

        Inventory::create([
            'product_id' => $this->productPhysical->id,
            'location_id' => $this->locationBango->id,
            'quantity' => 50,
        ]);

        $this->cashMethod = PaymentMethod::create([
            'name' => 'Tunai',
            'code' => 'CASH',
            'type' => 'CASH',
            'status' => 'ACTIVE',
        ]);

        $this->cashAccount = BalanceAccount::create([
            'name' => 'Kas Utama',
            'code' => 'CASH',
            'account_type' => 'CASH',
            'current_balance' => 1000000,
            'status' => 'ACTIVE',
            'location_id' => $this->locationBango->id,
        ]);
    }

    public function test_pos_checkout_livewire_component_creates_sale_and_audit_log(): void
    {
        $this->actingAs($this->cashier);

        Livewire::test(Checkout::class)
            ->call('addToCart', $this->productPhysical->id)
            ->call('setPaymentAmount', 25000)
            ->call('processCheckout')
            ->assertSet('showSuccessModal', true);

        $this->assertDatabaseHas('sales', [
            'cashier_id' => $this->cashier->id,
            'location_id' => $this->locationBango->id,
            'total_amount' => 25000,
            'status' => 'COMPLETED',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'POS_CHECKOUT',
            'user_id' => $this->cashier->id,
            'location_id' => $this->locationBango->id,
        ]);
    }

    public function test_idempotency_prevents_duplicate_checkout_sales(): void
    {
        $this->actingAs($this->cashier);
        $idempotencyKey = 'IDEM-KEY-TEST-999';

        $posService = app(PosService::class);
        $cartItems = [
            ['product' => $this->productPhysical, 'quantity' => 1],
        ];
        $payments = [
            ['payment_method_id' => $this->cashMethod->id, 'balance_account_id' => $this->cashAccount->id, 'amount' => 25000],
        ];

        $sale1 = $posService->processCheckout($this->cashier, $cartItems, $payments, 'First attempt', $idempotencyKey);
        $sale2 = $posService->processCheckout($this->cashier, $cartItems, $payments, 'Second attempt retry', $idempotencyKey);

        $this->assertEquals($sale1->id, $sale2->id);
        $this->assertEquals(1, Sale::where('idempotency_key', $idempotencyKey)->count());
    }

    public function test_location_scope_trait_restricts_non_owner(): void
    {
        $saleBango = Sale::create([
            'invoice_number' => 'TRX-BANGO-001',
            'cashier_id' => $this->cashier->id,
            'location_id' => $this->locationBango->id,
            'transaction_date' => now(),
            'subtotal' => 25000,
            'discount_amount' => 0,
            'total_amount' => 25000,
            'amount_paid' => 25000,
            'status' => 'COMPLETED',
        ]);

        $saleDuren = Sale::create([
            'invoice_number' => 'TRX-DUREN-001',
            'cashier_id' => $this->cashier->id,
            'location_id' => $this->locationDuren->id,
            'transaction_date' => now(),
            'subtotal' => 50000,
            'discount_amount' => 0,
            'total_amount' => 50000,
            'amount_paid' => 50000,
            'status' => 'COMPLETED',
        ]);

        // Cashier only sees Bango sales
        $cashierScoped = Sale::forUserLocation($this->cashier)->get();
        $this->assertTrue($cashierScoped->contains('id', $saleBango->id));
        $this->assertFalse($cashierScoped->contains('id', $saleDuren->id));

        // Owner sees all sales across locations
        $ownerScoped = Sale::forUserLocation($this->owner)->get();
        $this->assertTrue($ownerScoped->contains('id', $saleBango->id));
        $this->assertTrue($ownerScoped->contains('id', $saleDuren->id));
    }

    public function test_trash_restore_workflow_and_auto_retention_purge(): void
    {
        $this->actingAs($this->cashier);

        $sale = app(PosService::class)->processCheckout(
            $this->cashier,
            [['product' => $this->productPhysical, 'quantity' => 2]],
            [['payment_method_id' => $this->cashMethod->id, 'balance_account_id' => $this->cashAccount->id, 'amount' => 50000]]
        );

        $cancellationService = app(SaleCancellationService::class);

        // Move to trash
        $cancellationService->moveToTrash($sale, $this->cashier, 'Wrong customer order');
        $sale->refresh();
        $this->assertEquals('TRASHED', $sale->status);
        $this->assertDatabaseHas('audit_logs', ['action' => 'SALE_TRASH']);

        // Restore from trash
        $cancellationService->restoreFromTrash($sale, $this->cashier);
        $sale->refresh();
        $this->assertEquals('COMPLETED', $sale->status);
        $this->assertDatabaseHas('audit_logs', ['action' => 'SALE_RESTORE']);

        // Simulate expired 30-day retention
        $sale->update([
            'status' => 'TRASHED',
            'trashed_at' => now()->subDays(31),
        ]);

        (new PurgeExpiredTrashedSalesJob)->handle($cancellationService);

        $sale->refresh();
        $this->assertEquals('DELETED', $sale->status);
        $this->assertDatabaseHas('audit_logs', ['action' => 'AUTO_RETENTION_PURGE']);
    }

    public function test_non_owner_without_location_id_returns_empty_result_set(): void
    {
        $orphanRole = Role::create(['name' => 'STAFF', 'display_name' => 'Staff']);
        $orphanUser = User::create([
            'name' => 'Orphan User',
            'username' => 'orphanuser',
            'password' => bcrypt('password'),
            'role_id' => $orphanRole->id,
            'location_id' => null, // Unconfigured location
            'status' => 'ACTIVE',
        ]);

        Sale::create([
            'invoice_number' => 'TRX-ORPHAN-001',
            'cashier_id' => $this->cashier->id,
            'location_id' => $this->locationBango->id,
            'transaction_date' => now(),
            'subtotal' => 25000,
            'discount_amount' => 0,
            'total_amount' => 25000,
            'amount_paid' => 25000,
            'status' => 'COMPLETED',
        ]);

        $results = Sale::forUserLocation($orphanUser)->get();
        $this->assertCount(0, $results);
    }

    public function test_stock_opname_rejects_non_physical_product_service_guard(): void
    {
        $digitalProduct = Product::create([
            'code' => 'DIG-001',
            'name' => 'Pulsa Telkomsel 50k',
            'product_type' => 'DIGITAL',
            'cost_price' => 49000,
            'selling_price' => 52000,
        ]);

        $opname = \App\Models\StockOpname::create([
            'opname_number' => 'SOP-DIG-001',
            'location_id' => $this->locationBango->id,
            'status' => 'DRAFT',
            'started_at' => now(),
            'created_by' => $this->owner->id,
        ]);

        \App\Models\StockOpnameItem::create([
            'stock_opname_id' => $opname->id,
            'product_id' => $digitalProduct->id,
            'system_quantity' => 0,
            'physical_quantity' => 10,
            'difference' => 10,
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Stock Opname hanya berlaku untuk produk fisik');

        app(\App\Services\InventoryService::class)->approveStockOpname($opname, $this->owner);
    }

    public function test_admin_livewire_components_enforce_location_scoping(): void
    {
        $saleBango = Sale::create([
            'invoice_number' => 'TRX-LIVEWIRE-BANGO',
            'cashier_id' => $this->cashier->id,
            'location_id' => $this->locationBango->id,
            'transaction_date' => now(),
            'subtotal' => 25000,
            'discount_amount' => 0,
            'total_amount' => 25000,
            'amount_paid' => 25000,
            'status' => 'COMPLETED',
        ]);

        $saleDuren = Sale::create([
            'invoice_number' => 'TRX-LIVEWIRE-DUREN',
            'cashier_id' => $this->cashier->id,
            'location_id' => $this->locationDuren->id,
            'transaction_date' => now(),
            'subtotal' => 50000,
            'discount_amount' => 0,
            'total_amount' => 50000,
            'amount_paid' => 50000,
            'status' => 'COMPLETED',
        ]);

        $this->actingAs($this->cashier);

        // Sales Livewire Component only shows Bango transactions and ignores opening Duren detail
        Livewire::test(\App\Livewire\Admin\Sales::class)
            ->call('openDetailModal', $saleDuren->id)
            ->assertViewHas('selectedSale', null)
            ->call('openDetailModal', $saleBango->id)
            ->assertViewHas('selectedSale', function ($s) use ($saleBango) {
                return $s && $s->id === $saleBango->id;
            });

        // SampahTransaksi Livewire Component
        $saleDuren->update(['status' => 'TRASHED', 'trashed_at' => now()]);
        Livewire::test(\App\Livewire\Admin\SampahTransaksi::class)
            ->assertDontSee('TRX-LIVEWIRE-DUREN');

        // Inventories Livewire Component enforces location_id and options
        Livewire::test(\App\Livewire\Admin\Inventories::class)
            ->assertSet('selectedLocationId', $this->locationBango->id)
            ->assertViewHas('locations', function ($locs) {
                return $locs->count() === 1 && $locs->first()->id === $this->locationBango->id;
            });
    }

    public function test_finance_report_service_and_reports_component_enforce_location_scoping(): void
    {
        // Bango Sale 25,000
        Sale::create([
            'invoice_number' => 'TRX-RPT-BANGO',
            'cashier_id' => $this->cashier->id,
            'location_id' => $this->locationBango->id,
            'transaction_date' => now(),
            'subtotal' => 25000,
            'discount_amount' => 0,
            'total_amount' => 25000,
            'amount_paid' => 25000,
            'status' => 'COMPLETED',
        ]);

        // Duren Sale 100,000
        Sale::create([
            'invoice_number' => 'TRX-RPT-DUREN',
            'cashier_id' => $this->cashier->id,
            'location_id' => $this->locationDuren->id,
            'transaction_date' => now(),
            'subtotal' => 100000,
            'discount_amount' => 0,
            'total_amount' => 100000,
            'amount_paid' => 100000,
            'status' => 'COMPLETED',
        ]);

        $reportService = app(\App\Services\FinanceReportService::class);

        // Branch Cashier (Bango) metrics only sum Bango sale (25,000)
        $cashierMetrics = $reportService->getSummaryMetrics(user: $this->cashier);
        $this->assertEquals(25000, $cashierMetrics['omset']);

        // Owner metrics sum all sales (125,000)
        $ownerMetrics = $reportService->getSummaryMetrics(user: $this->owner);
        $this->assertEquals(125000, $ownerMetrics['omset']);

        // Reports Livewire component for Cashier sees 25,000 omset
        $this->actingAs($this->cashier);
        Livewire::test(\App\Livewire\Admin\Reports::class)
            ->assertViewHas('metrics', function ($m) {
                return $m['omset'] == 25000;
            });
    }

    public function test_pos_service_and_checkout_enforce_balance_account_location_scoping(): void
    {
        $durenCashAccount = BalanceAccount::create([
            'code' => 'CASH-DUREN',
            'name' => 'Kas Duren Sawit',
            'account_type' => 'CASH',
            'current_balance' => 100000,
            'status' => 'ACTIVE',
            'location_id' => $this->locationDuren->id,
        ]);

        $this->actingAs($this->cashier);

        // Checkout Livewire component default and accounts list exclude foreign location accounts
        Livewire::test(\App\Livewire\Pos\Checkout::class)
            ->assertViewHas('balanceAccounts', function ($accounts) use ($durenCashAccount) {
                return ! $accounts->contains('id', $durenCashAccount->id);
            });

        // PosService rejects payment targeted to another branch's balance account
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Akun saldo pembayaran tidak valid, tidak aktif, atau di luar lokasi cabang Anda.');

        app(PosService::class)->processCheckout(
            $this->cashier,
            [['product' => $this->productPhysical, 'quantity' => 1]],
            [['payment_method_id' => $this->cashMethod->id, 'balance_account_id' => $durenCashAccount->id, 'amount' => 25000]]
        );
    }

    public function test_balances_component_and_balance_service_enforce_location_scoping(): void
    {
        $durenCashAccount = BalanceAccount::create([
            'code' => 'CASH-DUREN-BAL',
            'name' => 'Kas Duren Balances',
            'account_type' => 'CASH',
            'current_balance' => 500000,
            'status' => 'ACTIVE',
            'location_id' => $this->locationDuren->id,
        ]);

        $balanceService = app(\App\Services\BalanceService::class);

        // Branch Cashier cannot transfer or adjust another branch's balance account
        $this->expectException(\InvalidArgumentException::class);
        $balanceService->deposit($durenCashAccount, 100000, 'Illegal deposit', $this->cashier);
    }
}
