<?php

namespace Tests\Feature;

use App\Models\BalanceAccount;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\PosService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_pos_checkout_single_payment_exact_amount(): void
    {
        $location = Location::where('code', 'RAJA-BANGO')->first();
        $cashier = User::where('username', 'superadmin')->first();
        $posService = app(PosService::class);
        $inventoryService = app(InventoryService::class);

        $product = Product::create([
            'code' => 'ACC-POS-01',
            'name' => 'Kabel Data Type-C',
            'product_type' => 'PHYSICAL',
            'cost_price' => 10000,
            'selling_price' => 25000,
        ]);

        // Add 10 initial stock
        $inventoryService->adjustStock($product, $location, 10, 'ADJUSTMENT_IN', 'Initial stock', $cashier);

        $cashPm = PaymentMethod::where('code', 'CASH')->first();
        $cashAccount = BalanceAccount::where('code', 'CASH')->first();

        $cartItems = [
            ['product' => $product, 'quantity' => 2],
        ];

        $paymentsData = [
            [
                'payment_method_id' => $cashPm->id,
                'balance_account_id' => $cashAccount->id,
                'amount' => 50000,
            ],
        ];

        $sale = $posService->processCheckout($cashier, $cartItems, $paymentsData, 'Test POS');

        $this->assertNotNull($sale);
        $this->assertEquals('COMPLETED', $sale->status);
        $this->assertEquals(50000, $sale->total_amount);
        $this->assertEquals(50000, $sale->amount_paid);
        $this->assertEquals(0, $sale->change_amount);
        $this->assertEquals(20000, $sale->total_cost);
        $this->assertEquals(30000, $sale->gross_profit);

        // Verify stock deducted from 10 to 8
        $inventory = Inventory::where('product_id', $product->id)->first();
        $this->assertEquals(8, $inventory->quantity);

        // Verify balance updated
        $this->assertEquals(50000, $cashAccount->fresh()->current_balance);

        // Verify balance transaction created
        $this->assertDatabaseHas('balance_transactions', [
            'destination_account_id' => $cashAccount->id,
            'transaction_type' => 'SALE_RECEIPT',
            'amount' => 50000,
        ]);
    }

    public function test_pos_checkout_multi_payment_with_change_amount(): void
    {
        $location = Location::where('code', 'RAJA-BANGO')->first();
        $cashier = User::where('username', 'superadmin')->first();
        $posService = app(PosService::class);
        $inventoryService = app(InventoryService::class);

        $product = Product::create([
            'code' => 'ACC-POS-02',
            'name' => 'Tempered Glass Vivan',
            'product_type' => 'PHYSICAL',
            'cost_price' => 20000,
            'selling_price' => 60000,
        ]);

        $inventoryService->adjustStock($product, $location, 5, 'ADJUSTMENT_IN', 'Stock', $cashier);

        $cashPm = PaymentMethod::where('code', 'CASH')->first();
        $qrisPm = PaymentMethod::where('code', 'QRIS')->first();

        $cashAccount = BalanceAccount::where('code', 'CASH')->first();
        $qrisAccount = BalanceAccount::where('code', 'QRIS')->first();

        $cartItems = [
            ['product' => $product, 'quantity' => 1], // Total 60,000
        ];

        // Customer pays 50,000 Cash + 30,000 QRIS = Total Paid 80,000 (Change 20,000)
        $paymentsData = [
            ['payment_method_id' => $cashPm->id, 'balance_account_id' => $cashAccount->id, 'amount' => 50000],
            ['payment_method_id' => $qrisPm->id, 'balance_account_id' => $qrisAccount->id, 'amount' => 30000],
        ];

        $sale = $posService->processCheckout($cashier, $cartItems, $paymentsData);

        $this->assertEquals(60000, $sale->total_amount);
        $this->assertEquals(80000, $sale->amount_paid);
        $this->assertEquals(20000, $sale->change_amount);

        // QRIS gets +30,000. Cash gets +50,000 minus 20,000 change = net +30,000.
        $this->assertEquals(30000, $qrisAccount->fresh()->current_balance);
        $this->assertEquals(30000, $cashAccount->fresh()->current_balance);
    }

    public function test_pos_checkout_blocks_insufficient_stock(): void
    {
        $location = Location::where('code', 'RAJA-BANGO')->first();
        $cashier = User::where('username', 'superadmin')->first();
        $posService = app(PosService::class);
        $inventoryService = app(InventoryService::class);

        $product = Product::create([
            'code' => 'ACC-POS-03',
            'name' => 'Headset Bluetooth',
            'product_type' => 'PHYSICAL',
            'cost_price' => 50000,
            'selling_price' => 100000,
        ]);

        $inventoryService->adjustStock($product, $location, 2, 'ADJUSTMENT_IN', 'Stock', $cashier);

        $cashPm = PaymentMethod::where('code', 'CASH')->first();
        $cashAccount = BalanceAccount::where('code', 'CASH')->first();

        $cartItems = [
            ['product' => $product, 'quantity' => 5], // Only 2 available
        ];

        $paymentsData = [
            ['payment_method_id' => $cashPm->id, 'balance_account_id' => $cashAccount->id, 'amount' => 500000],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $posService->processCheckout($cashier, $cartItems, $paymentsData);
    }

    public function test_pos_checkout_blocks_incomplete_price_status(): void
    {
        $cashier = User::where('username', 'superadmin')->first();
        $posService = app(PosService::class);

        $incompleteProduct = Product::create([
            'code' => 'INC-001',
            'name' => 'Kabel Rusak Harga 0',
            'product_type' => 'PHYSICAL',
            'cost_price' => 0,
            'selling_price' => 20000,
        ]);

        $cashPm = PaymentMethod::where('code', 'CASH')->first();
        $cashAccount = BalanceAccount::where('code', 'CASH')->first();

        $cartItems = [
            ['product' => $incompleteProduct, 'quantity' => 1],
        ];

        $paymentsData = [
            ['payment_method_id' => $cashPm->id, 'balance_account_id' => $cashAccount->id, 'amount' => 20000],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $posService->processCheckout($cashier, $cartItems, $paymentsData);
    }

    public function test_thermal_receipt_route(): void
    {
        $location = Location::where('code', 'RAJA-BANGO')->first();
        $superadmin = User::where('username', 'superadmin')->first();

        $product = Product::create([
            'code' => 'ACC-RCT-01',
            'name' => 'Kabel Micro 2m',
            'product_type' => 'PHYSICAL',
            'cost_price' => 5000,
            'selling_price' => 15000,
        ]);

        app(InventoryService::class)->adjustStock($product, $location, 10, 'ADJUSTMENT_IN', 'Stock', $superadmin);

        $cashPm = PaymentMethod::where('code', 'CASH')->first();
        $cashAccount = BalanceAccount::where('code', 'CASH')->first();

        $sale = app(PosService::class)->processCheckout(
            cashier: $superadmin,
            cartItems: [['product' => $product, 'quantity' => 1]],
            paymentsData: [['payment_method_id' => $cashPm->id, 'balance_account_id' => $cashAccount->id, 'amount' => 20000]]
        );

        $response = $this->actingAs($superadmin)->get('/receipt/thermal/' . $sale->id);
        $response->assertStatus(200);
        $response->assertSee('Raja Aksesoris');
        $response->assertSee($sale->invoice_number);
    }
}
