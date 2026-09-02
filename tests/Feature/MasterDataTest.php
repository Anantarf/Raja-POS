<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_category_and_brand_crud_and_soft_delete(): void
    {
        $category = Category::create([
            'name' => 'Aksesoris HP',
            'status' => 'ACTIVE',
        ]);
        $this->assertEquals('aksesoris-hp', $category->slug);

        $brand = Brand::create([
            'name' => 'ACOME',
            'status' => 'ACTIVE',
        ]);
        $this->assertEquals('acome', $brand->slug);

        // Soft delete test
        $category->delete();
        $this->assertSoftDeleted($category);

        $brand->delete();
        $this->assertSoftDeleted($brand);
    }

    public function test_product_creation_and_automatic_price_status_calculation(): void
    {
        $category = Category::create(['name' => 'Kabel Data', 'status' => 'ACTIVE']);
        $brand = Brand::create(['name' => 'Vivan', 'status' => 'ACTIVE']);

        // 1. Incomplete price product (cost_price = 0)
        $incompleteProduct = Product::create([
            'code' => 'KBL-001',
            'name' => 'Kabel Type-C 1m',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'product_type' => 'PHYSICAL',
            'cost_price' => 0,
            'selling_price' => 25000,
            'minimum_stock' => 3,
            'status' => 'ACTIVE',
        ]);
        $this->assertEquals('INCOMPLETE', $incompleteProduct->price_status);

        // 2. Complete price product
        $completeProduct = Product::create([
            'code' => 'KBL-002',
            'name' => 'Kabel Micro 1m',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'product_type' => 'PHYSICAL',
            'cost_price' => 15000,
            'selling_price' => 25000,
            'minimum_stock' => 3,
            'status' => 'ACTIVE',
        ]);
        $this->assertEquals('COMPLETE', $completeProduct->price_status);

        // 3. Updating prices recalculates price_status
        $incompleteProduct->update(['cost_price' => 10000]);
        $this->assertEquals('COMPLETE', $incompleteProduct->fresh()->price_status);
    }

    public function test_effective_barcode_fallback(): void
    {
        $productWithoutBarcode = Product::create([
            'code' => 'RJA-ACOM-0001',
            'barcode' => null,
            'name' => 'Mic Acome',
            'product_type' => 'PHYSICAL',
            'cost_price' => 100000,
            'selling_price' => 150000,
        ]);
        $this->assertEquals('RJA-ACOM-0001', $productWithoutBarcode->effective_barcode);

        $productWithBarcode = Product::create([
            'code' => 'RJA-ACOM-0002',
            'barcode' => '8991234567890',
            'name' => 'Speaker Acome',
            'product_type' => 'PHYSICAL',
            'cost_price' => 100000,
            'selling_price' => 150000,
        ]);
        $this->assertEquals('8991234567890', $productWithBarcode->effective_barcode);
    }

    public function test_permission_check_for_cost_price_view(): void
    {
        $owner = User::where('username', 'superadmin')->first();

        $cashierRole = Role::where('name', 'CASHIER')->first();
        $cashier = User::create([
            'name' => 'Kasir 1',
            'username' => 'kasir1',
            'password' => bcrypt('password'),
            'role_id' => $cashierRole->id,
            'status' => 'ACTIVE',
        ]);

        $this->assertTrue($owner->hasPermission('cost_price.view'));
        $this->assertFalse($cashier->hasPermission('cost_price.view'));
    }
}
