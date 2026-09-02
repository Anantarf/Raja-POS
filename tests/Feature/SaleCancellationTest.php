<?php

namespace Tests\Feature;

use App\Models\BalanceAccount;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\PosService;
use App\Services\SaleCancellationService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleCancellationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_move_sale_to_sampah_transaksi_reverts_stock_and_balances(): void
    {
        $location = Location::where('code', 'RAJA-BANGO')->first();
        $owner = User::where('username', 'superadmin')->first();
        $posService = app(PosService::class);
        $inventoryService = app(InventoryService::class);
        $cancellationService = app(SaleCancellationService::class);

        $product = Product::create([
            'code' => 'ACC-TRASH-01',
            'name' => 'Charger Original Samsung',
            'product_type' => 'PHYSICAL',
            'cost_price' => 50000,
            'selling_price' => 100000,
        ]);

        // Initial stock 10
        $inventoryService->adjustStock($product, $location, 10, 'ADJUSTMENT_IN', 'Initial stock', $owner);

        $cashPm = PaymentMethod::where('code', 'CASH')->first();
        $cashAccount = BalanceAccount::where('code', 'CASH')->first();

        // 1. Process Sale (2 units, total 200,000, paid 200,000)
        $sale = $posService->processCheckout(
            cashier: $owner,
            cartItems: [['product' => $product, 'quantity' => 2]],
            paymentsData: [['payment_method_id' => $cashPm->id, 'balance_account_id' => $cashAccount->id, 'amount' => 200000]]
        );

        $this->assertEquals(8, Inventory::where('product_id', $product->id)->value('quantity'));
        $this->assertEquals(200000, $cashAccount->fresh()->current_balance);

        // 2. Move Sale to Sampah Transaksi
        $result = $cancellationService->moveToTrash($sale, $owner, 'Salah produk customer');
        $this->assertTrue($result);

        $sale->refresh();
        $this->assertEquals('TRASHED', $sale->status);
        $this->assertEquals('Salah produk customer', $sale->trash_reason);

        // Stock restored back from 8 to 10
        $this->assertEquals(10, Inventory::where('product_id', $product->id)->value('quantity'));

        // Balance deducted back from 200,000 to 0
        $this->assertEquals(0, $cashAccount->fresh()->current_balance);

        // Verify TRASH_RESTORE movement logged
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'movement_type' => 'TRASH_RESTORE',
            'quantity_change' => 2,
        ]);
    }

    public function test_cashier_cannot_move_sale_to_trash_permission_check(): void
    {
        $cashierRole = Role::where('name', 'CASHIER')->first();
        $cashier = User::create([
            'name' => 'Kasir Sampah Test',
            'username' => 'kasirsampah',
            'password' => bcrypt('password'),
            'role_id' => $cashierRole->id,
            'status' => 'ACTIVE',
        ]);

        $this->assertFalse($cashier->hasPermission('sales.trash'));
        $this->assertFalse($cashier->hasPermission('sales.restore'));
    }

    public function test_restore_sale_from_sampah_transaksi(): void
    {
        $location = Location::where('code', 'RAJA-BANGO')->first();
        $owner = User::where('username', 'superadmin')->first();
        $posService = app(PosService::class);
        $inventoryService = app(InventoryService::class);
        $cancellationService = app(SaleCancellationService::class);

        $product = Product::create([
            'code' => 'ACC-TRASH-02',
            'name' => 'Memory Card 64GB',
            'product_type' => 'PHYSICAL',
            'cost_price' => 40000,
            'selling_price' => 80000,
        ]);

        $inventoryService->adjustStock($product, $location, 5, 'ADJUSTMENT_IN', 'Stock', $owner);

        $cashPm = PaymentMethod::where('code', 'CASH')->first();
        $cashAccount = BalanceAccount::where('code', 'CASH')->first();

        $sale = $posService->processCheckout(
            cashier: $owner,
            cartItems: [['product' => $product, 'quantity' => 1]],
            paymentsData: [['payment_method_id' => $cashPm->id, 'balance_account_id' => $cashAccount->id, 'amount' => 80000]]
        );

        // Move to trash
        $cancellationService->moveToTrash($sale, $owner, 'Batal');
        $this->assertEquals(5, Inventory::where('product_id', $product->id)->value('quantity'));

        // Restore from trash
        $result = $cancellationService->restoreFromTrash($sale, $owner);
        $this->assertTrue($result);

        $sale->refresh();
        $this->assertEquals('COMPLETED', $sale->status);
        $this->assertEquals(4, Inventory::where('product_id', $product->id)->value('quantity'));
        $this->assertEquals(80000, $cashAccount->fresh()->current_balance);
    }

    public function test_30_day_auto_delete_retention(): void
    {
        $location = Location::where('code', 'RAJA-BANGO')->first();
        $owner = User::where('username', 'superadmin')->first();
        $cancellationService = app(SaleCancellationService::class);
        $inventoryService = app(InventoryService::class);

        $product = Product::create([
            'code' => 'ACC-TRASH-03',
            'name' => 'Softcase Bening',
            'product_type' => 'PHYSICAL',
            'cost_price' => 5000,
            'selling_price' => 15000,
        ]);

        $inventoryService->adjustStock($product, $location, 5, 'ADJUSTMENT_IN', 'Stock', $owner);

        $cashPm = PaymentMethod::where('code', 'CASH')->first();
        $cashAccount = BalanceAccount::where('code', 'CASH')->first();

        $sale = app(PosService::class)->processCheckout(
            cashier: $owner,
            cartItems: [['product' => $product, 'quantity' => 1]],
            paymentsData: [['payment_method_id' => $cashPm->id, 'balance_account_id' => $cashAccount->id, 'amount' => 15000]]
        );

        $cancellationService->moveToTrash($sale, $owner, 'Retensi test');

        // Travel 31 days into future
        $sale->update(['trashed_at' => now()->subDays(31)]);

        $count = $cancellationService->apply30DayAutoDeleteRetention();
        $this->assertEquals(1, $count);

        $sale->refresh();
        $this->assertEquals('DELETED', $sale->status);

        // Restore attempt after DELETED throws exception
        $this->expectException(\InvalidArgumentException::class);
        $cancellationService->restoreFromTrash($sale, $owner);
    }
}
