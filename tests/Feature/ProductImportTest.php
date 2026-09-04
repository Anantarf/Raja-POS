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
            'IMP-003,Jasa Pasang Tempered Glass,Jasa,,LAYANAN,0,10000,,0,0,',
        ]);

        Storage::fake('local');
        Storage::disk('local')->put('test_import.csv', $csvContent);
        $filePath = Storage::disk('local')->path('test_import.csv');

        $result = $importService->importFromCsv($filePath, $superadmin);

        $this->assertEquals(3, $result['imported_count']);
        $this->assertEquals(0, $result['updated_count']);
        $this->assertEquals(1, $result['incomplete_count']); // Digital item has no modal; layanan may use nominal transaksi.
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

    public function test_generate_and_import_formatted_excel_template(): void
    {
        $service = app(ProductImportService::class);
        $path = $service->generateExcelTemplate();

        $this->assertFileExists($path);
        $this->assertSame('PK', file_get_contents($path, false, null, 0, 2));

        $result = $service->importFromExcel($path, User::where('username', 'superadmin')->first());
        @unlink($path);

        $this->assertEquals(3, $result['imported_count']);
        $this->assertEmpty($result['errors']);
    }

    public function test_export_products_to_formatted_excel(): void
    {
        Product::create([
            'code' => 'EXP-001',
            'name' => 'Produk Export Excel',
            'product_type' => 'PHYSICAL',
            'cost_price' => 10000,
            'selling_price' => 20000,
        ]);

        $path = app(ProductImportService::class)->exportProductsToExcel();
        $this->assertFileExists($path);
        $this->assertSame('PK', file_get_contents($path, false, null, 0, 2));
        @unlink($path);
    }

    public function test_import_parses_indonesian_thousand_separators(): void
    {
        $superadmin = User::where('username', 'superadmin')->first();
        $csv = "Kode,Nama,Jenis Stok,Harga Modal,Harga Jual,Stok Awal\nIMP-RP,Kabel Rupiah,PHYSICAL,15.000,35.000,3";

        Storage::fake('local');
        Storage::disk('local')->put('rupiah.csv', $csv);
        $path = Storage::disk('local')->path('rupiah.csv');

        $result = app(ProductImportService::class)->importFromCsv($path, $superadmin);
        $product = Product::where('code', 'IMP-RP')->firstOrFail();

        $this->assertEmpty($result['errors']);
        $this->assertEquals(15000, $product->cost_price);
        $this->assertEquals(35000, $product->selling_price);
    }
    public function test_reimport_does_not_add_initial_stock_twice(): void
    {
        $superadmin = User::where('username', 'superadmin')->first();
        $csv = "Kode,Nama,Jenis Stok,Harga Modal,Harga Jual,Stok Awal\nIMP-IDEMP,Kabel Idempotent,PHYSICAL,10000,20000,8";
        Storage::fake('local');
        Storage::disk('local')->put('idempotent.csv', $csv);
        $path = Storage::disk('local')->path('idempotent.csv');

        $service = app(ProductImportService::class);
        $service->importFromCsv($path, $superadmin);
        $service->importFromCsv($path, $superadmin);

        $product = Product::where('code', 'IMP-IDEMP')->firstOrFail();
        $this->assertEquals(8, Inventory::where('product_id', $product->id)->value('quantity'));
    }
}
