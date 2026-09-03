<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductImportService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_import_products_from_csv(): void
    {
        $superadmin = User::where('username', 'superadmin')->first();
        $importService = app(ProductImportService::class);

        // Create temporary CSV content
        $csvContent = implode("\n", [
            'Kode,Nama,Kategori,Brand,Tipe,Harga Modal,Harga Jual,Barcode,Stok Awal,Stok Minimum,Provider Akun',
            'IMP-001,Kabel Type-C 1m,Aksesoris Hp,Vivan,PHYSICAL,15000,35000,8991234567890,20,5,',
            'IMP-002,Pulsa PLN 50rb,Pulsa,PLN,DIGITAL,0,52500,,0,0,DANA',
            'IMP-003,Jasa Pasang Tempered Glass,Jasa,,SERVICE,0,10000,,0,0,',
        ]);

        Storage::fake('local');
        Storage::disk('local')->put('test_import.csv', $csvContent);
        $filePath = Storage::disk('local')->path('test_import.csv');

        $result = $importService->importFromCsv($filePath, $superadmin);

        $this->assertEquals(3, $result['imported_count']);
        $this->assertEquals(0, $result['updated_count']);
        $this->assertEquals(2, $result['incomplete_count']); // IMP-002 & IMP-003 have cost 0
        $this->assertEmpty($result['errors']);

        // Assert Product IMP-001 created
        $p1 = Product::where('code', 'IMP-001')->first();
        $this->assertNotNull($p1);
        $this->assertEquals('Kabel Type-C 1m', $p1->name);
        $this->assertEquals('COMPLETE', $p1->price_status);
        $this->assertEquals(15000, $p1->cost_price);
        $this->assertEquals(35000, $p1->selling_price);

        // Assert Category & Brand auto created
        $cat = Category::where('name', 'Aksesoris Hp')->first();
        $this->assertNotNull($cat);

        $brand = Brand::where('name', 'Vivan')->first();
        $this->assertNotNull($brand);

        // Assert Physical Inventory created with stock 20
        $inv = Inventory::where('product_id', $p1->id)->first();
        $this->assertNotNull($inv);
        $this->assertEquals(20, $inv->quantity);

        // Assert Product IMP-002 (Digital)
        $p2 = Product::where('code', 'IMP-002')->first();
        $this->assertNotNull($p2);
        $this->assertEquals('INCOMPLETE', $p2->price_status);
        $this->assertEquals('DIGITAL', $p2->product_type);
    }

    public function test_generate_csv_template(): void
    {
        $importService = app(ProductImportService::class);
        $template = $importService->generateCsvTemplate();

        $this->assertStringContainsString('Kode Produk', $template);
        $this->assertStringContainsString('Nama Barang/Layanan', $template);
        $this->assertStringContainsString('ACOME selfie stick SS07A black', $template);
    }
}
