<?php

namespace App\Services;

use App\Models\BalanceAccount;
use App\Models\Brand;
use App\Models\Category;
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
        $idxCode = $this->findHeaderIndex($headers, ['kode', 'sku', 'code']);
        $idxName = $this->findHeaderIndex($headers, ['nama', 'name', 'nama produk']);
        $idxCategory = $this->findHeaderIndex($headers, ['kategori', 'category']);
        $idxBrand = $this->findHeaderIndex($headers, ['brand', 'merk']);
        $idxType = $this->findHeaderIndex($headers, ['tipe', 'type', 'tipe produk']);
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
                $errors[] = "Baris {$rowNumber}: Nama produk wajib diisi.";
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
            'Kode',
            'Nama',
            'Kategori',
            'Brand',
            'Tipe',
            'Harga Modal',
            'Harga Jual',
            'Barcode',
            'Stok Awal',
            'Stok Minimum',
            'Provider Akun',
        ];

        $sample1 = [
            'ACC-001',
            'Kabel Data Type-C Fast Charge 1m',
            'Kabel & Adaptor',
            'Vivan',
            'PHYSICAL',
            '15000',
            '35000',
            '8991234567890',
            '20',
            '5',
            '',
        ];

        $sample2 = [
            'DIG-PLN-01',
            'Pulsa Token PLN 50rb',
            'Pulsa & Listrik',
            'PLN',
            'DIGITAL',
            '50500',
            '52500',
            '',
            '0',
            '0',
            'DANA',
        ];

        $sample3 = [
            'SVC-JASA-01',
            'Jasa Pasang Tempered Glass',
            'Jasa Service',
            '',
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
        foreach ($rows as $row) {
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
