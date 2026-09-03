<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockOpname;
use App\Models\User;
use App\Services\InventoryService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_inventory_creation_and_stock_status_calculation(): void
    {
        $location = Location::where('code', 'RAJA-BANGO')->first();
        $product = Product::create([
            'code' => 'ACC-001',
            'name' => 'Tempered Glass iPhone 11',
            'product_type' => 'PHYSICAL',
            'cost_price' => 15000,
            'selling_price' => 35000,
            'minimum_stock' => 3,
        ]);

        $inventory = Inventory::create([
            'product_id' => $product->id,
            'location_id' => $location->id,
            'quantity' => 0,
        ]);
        $this->assertEquals('OUT_OF_STOCK', $inventory->stock_status);
        $this->assertEquals('HABIS', $inventory->stock_status_label);

        $inventory->update(['quantity' => 2]);
        $this->assertEquals('LOW_STOCK', $inventory->fresh()->stock_status);
        $this->assertEquals('MENIPIS', $inventory->fresh()->stock_status_label);

        $inventory->update(['quantity' => 10]);
        $this->assertEquals('AVAILABLE', $inventory->fresh()->stock_status);
        $this->assertEquals('TERSEDIA', $inventory->fresh()->stock_status_label);
    }

    public function test_inventory_service_stock_adjustment(): void
    {
        $location = Location::where('code', 'RAJA-BANGO')->first();
        $owner = User::where('username', 'superadmin')->first();
        $inventoryService = app(InventoryService::class);

        $physicalProduct = Product::create([
            'code' => 'ACC-002',
            'name' => 'Kabel Type C Acome',
            'product_type' => 'PHYSICAL',
            'cost_price' => 10000,
            'selling_price' => 20000,
        ]);

        // 1. Initial Adjustment In
        $movement = $inventoryService->adjustStock(
            product: $physicalProduct,
            location: $location,
            quantityChange: 20,
            movementType: 'ADJUSTMENT_IN',
            notes: 'Stok awal',
            user: $owner
        );

        $this->assertNotNull($movement);
        $this->assertEquals(0, $movement->quantity_before);
        $this->assertEquals(20, $movement->quantity_change);
        $this->assertEquals(20, $movement->quantity_after);
        $this->assertEquals('ADJUSTMENT_IN', $movement->movement_type);

        // Verify inventory quantity
        $inventory = Inventory::where('product_id', $physicalProduct->id)->first();
        $this->assertEquals(20, $inventory->quantity);

        // 2. Digital Product Adjustment is skipped
        $digitalProduct = Product::create([
            'code' => 'DIG-001',
            'name' => 'Pulsa Telkomsel 50k',
            'product_type' => 'DIGITAL',
            'cost_price' => 49000,
            'selling_price' => 52000,
        ]);

        $digitalMovement = $inventoryService->adjustStock(
            product: $digitalProduct,
            location: $location,
            quantityChange: 10,
            movementType: 'ADJUSTMENT_IN',
            user: $owner
        );

        $this->assertNull($digitalMovement);
    }

    public function test_inventory_service_blocks_negative_stock(): void
    {
        $location = Location::where('code', 'RAJA-BANGO')->first();
        $owner = User::where('username', 'superadmin')->first();
        $inventoryService = app(InventoryService::class);

        $product = Product::create([
            'code' => 'ACC-NEG-001',
            'name' => 'Kabel Negative Guard',
            'product_type' => 'PHYSICAL',
            'cost_price' => 10000,
            'selling_price' => 20000,
        ]);

        $this->expectException(\InvalidArgumentException::class);

        $inventoryService->adjustStock(
            product: $product,
            location: $location,
            quantityChange: -1,
            movementType: 'SALE',
            user: $owner
        );
    }

    public function test_stock_opname_creation_and_approval_flow(): void
    {
        $location = Location::where('code', 'RAJA-BANGO')->first();
        $owner = User::where('username', 'superadmin')->first();
        $inventoryService = app(InventoryService::class);

        $product = Product::create([
            'code' => 'ACC-003',
            'name' => 'TWS Acome T1',
            'product_type' => 'PHYSICAL',
            'cost_price' => 120000,
            'selling_price' => 180000,
        ]);

        // Set initial stock to 12
        $inventoryService->adjustStock($product, $location, 12, 'ADJUSTMENT_IN', 'Initial stock', $owner);

        // Create Stock Opname session (System: 12, Physical: 10, Difference: -2)
        $opname = StockOpname::create([
            'opname_number' => 'SOP-20260903-0001',
            'location_id' => $location->id,
            'status' => 'DRAFT',
            'created_by' => $owner->id,
            'started_at' => now(),
        ]);

        $opname->items()->create([
            'product_id' => $product->id,
            'system_quantity' => 12,
            'physical_quantity' => 10,
            'difference' => -2,
            'notes' => '2 unit rusak/hilang',
        ]);

        $this->assertEquals('DRAFT', $opname->status);

        // Approve Opname
        $result = $inventoryService->approveStockOpname($opname, $owner);
        $this->assertTrue($result);

        $opname->refresh();
        $this->assertEquals('COMPLETED', $opname->status);
        $this->assertEquals($owner->id, $opname->approved_by);

        // Verify stock updated to 10 and movement created
        $inventory = Inventory::where('product_id', $product->id)->first();
        $this->assertEquals(10, $inventory->quantity);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'movement_type' => 'STOCK_OPNAME',
            'quantity_change' => -2,
            'quantity_after' => 10,
        ]);
    }

    public function test_stock_opname_uses_current_stock_at_approval(): void
    {
        $location = Location::where('code', 'RAJA-BANGO')->first();
        $owner = User::where('username', 'superadmin')->first();
        $product = Product::create([
            'code' => 'ACC-OPNAME-CURRENT',
            'name' => 'Produk Opname Terkini',
            'product_type' => 'PHYSICAL',
            'cost_price' => 10000,
            'selling_price' => 20000,
        ]);
        $service = app(InventoryService::class);
        $service->adjustStock($product, $location, 12, 'ADJUSTMENT_IN', 'Stok awal', $owner);

        $opname = StockOpname::create([
            'opname_number' => 'SOP-CURRENT-001',
            'location_id' => $location->id,
            'status' => 'DRAFT',
            'created_by' => $owner->id,
        ]);
        $item = $opname->items()->create([
            'product_id' => $product->id,
            'system_quantity' => 12,
            'physical_quantity' => 10,
            'difference' => -2,
        ]);

        $service->adjustStock($product, $location, -1, 'SALE', 'Penjualan setelah hitung', $owner);
        $service->approveStockOpname($opname, $owner);

        $this->assertEquals(10, Inventory::where('product_id', $product->id)->value('quantity'));
        $this->assertEquals(-1, $item->fresh()->difference);
    }

    public function test_inventories_page_sorting_by_stock_status(): void
    {
        $user = User::where('username', 'superadmin')->first();

        \Livewire\Livewire::actingAs($user)
            ->test(\App\Livewire\Admin\Inventories::class)
            ->call('sortBy', 'stock_status')
            ->assertSet('sortField', 'stock_status')
            ->assertSet('sortDirection', 'asc')
            ->call('sortBy', 'stock_status')
            ->assertSet('sortDirection', 'desc')
            ->assertStatus(200);
    }
}
