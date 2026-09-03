<?php

namespace App\Services;

use App\Models\BalanceAccount;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ProductImportService
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * Import products from a CSV/Excel file path.
     */
    public function importFromCsv(string $filePath, User $user): array
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("File tidak ditemukan pada path: {$filePath}");
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new InvalidArgumentException("Gagal membuka file CSV.");
        }

        // Read header row
        $rawHeaders = fgetcsv($handle, 2000, ',');
        if (!$rawHeaders) {
            fclose($handle);
            throw new InvalidArgumentException("File CSV kosong atau format header tidak terdeteksi.");
        }

        // Normalize headers
        $headers = array_map(fn ($h) => strtolower(trim(str_replace(['"', "'"], '', $h))), $rawHeaders);

        // Detect column indexes
        $idxCode = $this->findHeaderIndex($headers, ['kode produk', 'kode', 'sku', 'code']);
        $idxName = $this->findHeaderIndex($headers, ['nama barang/layanan', 'nama layanan', 'nama barang', 'nama', 'name', 'nama produk']);
        $idxCategory = $this->findHeaderIndex($headers, ['kategori', 'category']);
        $idxSubtype = $this->findHeaderIndex($headers, ['jenis', 'subtipe', 'subtype']);
        $idxBrand = $this->findHeaderIndex($headers, ['merk', 'brand']);
        $idxType = $this->findHeaderIndex($headers, ['jenis stok', 'tipe', 'type', 'tipe produk']);
        $idxCost = $this->findHeaderIndex($headers, ['harga modal', 'modal', 'cost_price']);
        $idxSelling = $this->findHeaderIndex($headers, ['harga jual', 'jual', 'selling_price']);
        $idxBarcode = $this->findHeaderIndex($headers, ['barcode']);
        $idxStock = $this->findHeaderIndex($headers, ['stok awal', 'stok', 'initial_stock']);
        $idxMinStock = $this->findHeaderIndex($headers, ['stok minimum', 'stok min', 'minimum_stock']);
        $idxAccount = $this->findHeaderIndex($headers, ['provider akun', 'akun provider', 'balance_account']);

        $rowNumber = 1; // Row 1 is header
        $importedCount = 0;
        $updatedCount = 0;
        $incompleteCount = 0;
        $errors = [];

        $location = Location::where('code', 'RAJA-BANGO')->first()
            ?? Location::where('status', 'ACTIVE')->first();

        while (($row = fgetcsv($handle, 2000, ',')) !== false) {
            $rowNumber++;

            // Skip completely empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            $name = $idxName !== null ? trim($row[$idxName] ?? '') : '';
            if (empty($name)) {
                $errors[] = "Baris {$rowNumber}: Nama barang/layanan wajib diisi.";
                continue;
            }

            $code = $idxCode !== null ? trim($row[$idxCode] ?? '') : '';
            if (empty($code)) {
                $code = 'PRD-' . strtoupper(Str::random(6));
            }

            $catName = $idxCategory !== null ? trim($row[$idxCategory] ?? '') : 'Umum';
            if (empty($catName)) {
                $catName = 'Umum';
            }

            $subtypeName = $idxSubtype !== null ? trim($row[$idxSubtype] ?? '') : '';
            $brandName = $idxBrand !== null ? trim($row[$idxBrand] ?? '') : '';
            $typeInput = $idxType !== null ? strtoupper(trim($row[$idxType] ?? '')) : 'PHYSICAL';

            $productType = match ($typeInput) {
                'DIGITAL' => 'DIGITAL',
                'SERVICE' => 'SERVICE',
                default => 'PHYSICAL',
            };

            $costPrice = $idxCost !== null ? (float) preg_replace('/[^0-9.]/', '', $row[$idxCost] ?? '0') : 0.0;
            $sellingPrice = $idxSelling !== null ? (float) preg_replace('/[^0-9.]/', '', $row[$idxSelling] ?? '0') : 0.0;
            $barcode = $idxBarcode !== null ? trim($row[$idxBarcode] ?? '') : null;
            $initialStock = $idxStock !== null ? (int) preg_replace('/[^0-9]/', '', $row[$idxStock] ?? '0') : 0;
            $minStock = $idxMinStock !== null ? (int) preg_replace('/[^0-9]/', '', $row[$idxMinStock] ?? '3') : 3;
            $accountName = $idxAccount !== null ? trim($row[$idxAccount] ?? '') : '';

            try {
                DB::transaction(function () use (
                    $code,
                    $name,
                    $catName,
                    $subtypeName,
                    $brandName,
                    $productType,
                    $costPrice,
                    $sellingPrice,
                    $barcode,
                    $initialStock,
                    $minStock,
                    $accountName,
                    $location,
                    $user,
                    &$importedCount,
                    &$updatedCount,
                    &$incompleteCount
                ) {
                    // Resolve Category
                    $category = Category::firstOrCreate(
                        ['name' => $catName],
                        ['slug' => Str::slug($catName), 'status' => 'ACTIVE']
                    );

                    // Resolve Brand
                    $brand = null;
                    if (!empty($brandName)) {
                        $brand = Brand::firstOrCreate(
                            ['name' => $brandName],
                            ['slug' => Str::slug($brandName), 'status' => 'ACTIVE']
                        );
                    }

                    // Resolve Balance Account for Provider if given
                    $balanceAccount = null;
                    if (!empty($accountName)) {
                        $balanceAccount = BalanceAccount::where('name', 'like', "%{$accountName}%")
                            ->orWhere('code', $accountName)
                            ->first();
                    }

                    $existingProduct = Product::where('code', $code)->first();

                    $product = Product::updateOrCreate(
                        ['code' => $code],
                        [
                            'name' => $name,
                            'slug' => Str::slug($name) . '-' . Str::lower(Str::random(4)),
                            'category_id' => $category->id,
                            'brand_id' => $brand?->id,
                            'product_type' => $productType,
                            'product_subtype' => !empty($subtypeName) ? $subtypeName : null,
                            'cost_price' => $costPrice,
                            'selling_price' => $sellingPrice,
                            'barcode' => !empty($barcode) ? $barcode : null,
                            'minimum_stock' => $minStock > 0 ? $minStock : 3,
                            'default_balance_account_id' => $balanceAccount?->id,
                            'status' => 'ACTIVE',
                        ]
                    );

                    if ($product->price_status === 'INCOMPLETE') {
                        $incompleteCount++;
                    }

                    if ($existingProduct) {
                        $updatedCount++;
                    } else {
                        $importedCount++;
                    }

                    // Initial Stock Adjustment for PHYSICAL products
                    if ($productType === 'PHYSICAL' && $initialStock > 0 && $location) {
                        $this->inventoryService->adjustStock(
                            product: $product,
                            location: $location,
                            quantityChange: $initialStock,
                            movementType: 'ADJUSTMENT_IN',
                            notes: 'Stok Awal Import Excel',
                            user: $user,
                            reference: $product
                        );
                    }
                });
            } catch (\Exception $e) {
                $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
            }
        }

        fclose($handle);

        return [
            'imported_count' => $importedCount,
            'updated_count' => $updatedCount,
            'incomplete_count' => $incompleteCount,
            'errors' => $errors,
        ];
    }

    /**
     * Generate template CSV string for download.
     */
    public function generateCsvTemplate(): string
    {
        $headers = [
            'Kode Produk',
            'Nama Barang/Layanan',
            'Kategori',
            'Jenis',
            'Merk',
            'Jenis Stok',
            'Modal',
            'Harga Jual',
            'Barcode',
            'Stok Awal',
            'Stok Minimum',
            'Provider Akun',
        ];

        $sample1 = [
            'RJA-ACOM-0004',
            'ACOME selfie stick SS07A black',
            'AKSESORIS',
            'TRIPOD',
            'ACOME',
            'FISIK',
            '40000',
            '80000',
            '8991234567890',
            '20',
            '5',
            '',
        ];

        $sample2 = [
            'RJA-XL-0001',
            'XL COMBO FLEX MAX - 7 GB 28 HARI',
            'Pulsa',
            'MULTI',
            'Xl',
            'DIGITAL',
            '32174',
            '35000',
            '',
            '0',
            '0',
            'MULTI',
        ];

        $sample3 = [
            'SVC-TG-0001',
            'Jasa Pasang Tempered Glass / Hydrogel',
            'Jasa Service',
            'SERVICE',
            'RAJA',
            'SERVICE',
            '0',
            '10000',
            '',
            '0',
            '0',
            '',
        ];

        $rows = [$headers, $sample1, $sample2, $sample3];

        $output = fopen('php://temp', 'r+');
        // Add UTF-8 BOM for Microsoft Excel native compatibility
        fwrite($output, "\xEF\xBB\xBF");
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }

    /**
     * Export all products to clean Excel-compatible CSV string with UTF-8 BOM.
     */
    public function exportProductsToExcel(?string $categoryId = null, ?string $productType = null): string
    {
        $query = Product::with(['category', 'brand', 'defaultBalanceAccount']);

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if (!empty($productType) && $productType !== 'ALL') {
            $query->where('product_type', $productType);
        }

        $products = $query->orderBy('name')->get();

        $headers = [
            'Kode Produk',
            'Nama Barang/Layanan',
            'Kategori',
            'Jenis',
            'Merk',
            'Jenis Stok',
            'Modal',
            'Harga Jual',
            'Barcode',
            'Stok',
            'Provider Akun',
            'Status Kelengkapan',
        ];

        $output = fopen('php://temp', 'r+');
        // Add UTF-8 BOM for Microsoft Excel native compatibility
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, $headers);

        foreach ($products as $product) {
            $inv = $product->product_type === 'PHYSICAL' ? Inventory::where('product_id', $product->id)->first() : null;
            $stock = $inv?->quantity ?? 0;

            $row = [
                $product->code,
                $product->name,
                $product->category?->name ?? 'Umum',
                $product->product_subtype ?? '',
                $product->brand?->name ?? '',
                $product->product_type,
                $product->cost_price ?? 0,
                $product->selling_price ?? 0,
                $product->effective_barcode ?? '',
                $product->product_type === 'PHYSICAL' ? $stock : '-',
                $product->defaultBalanceAccount?->name ?? '',
                $product->price_status === 'COMPLETE' ? 'Lengkap' : 'Harga Belum Lengkap',
            ];
            fputcsv($output, $row);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }

    protected function findHeaderIndex(array $headers, array $candidates): ?int
    {
        foreach ($candidates as $candidate) {
            $key = array_search(strtolower($candidate), $headers, true);
            if ($key !== false) {
                return (int) $key;
            }
        }

        return null;
    }
}
