<?php

namespace App\Services;

use App\Models\BalanceAccount;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;

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
        if (! file_exists($filePath)) {
            throw new InvalidArgumentException("File tidak ditemukan pada path: {$filePath}");
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new InvalidArgumentException('Gagal membuka file CSV.');
        }

        // Read header row
        $rawHeaders = fgetcsv($handle, 2000, ',');
        if (! $rawHeaders) {
            fclose($handle);
            throw new InvalidArgumentException('File CSV kosong atau format header tidak terdeteksi.');
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

        $location = $user->location;
        if (! $location || $location->status !== 'ACTIVE') {
            throw new InvalidArgumentException('Lokasi kerja pengguna belum diatur atau tidak aktif.');
        }

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
                $code = 'PRD-'.strtoupper(Str::random(6));
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
                'LAYANAN', 'SERVICE', 'JASA' => 'LAYANAN',
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
                    if (! empty($brandName)) {
                        $brand = Brand::firstOrCreate(
                            ['name' => $brandName],
                            ['slug' => Str::slug($brandName), 'status' => 'ACTIVE']
                        );
                    }

                    // Resolve Balance Account for Provider if given
                    $balanceAccount = null;
                    if (! empty($accountName)) {
                        $balanceAccount = BalanceAccount::where('name', 'like', "%{$accountName}%")
                            ->orWhere('code', $accountName)
                            ->first();
                    }

                    $existingProduct = Product::where('code', $code)->first();

                    $product = Product::updateOrCreate(
                        ['code' => $code],
                        [
                            'name' => $name,
                            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
                            'category_id' => $category->id,
                            'brand_id' => $brand?->id,
                            'product_type' => $productType,
                            'product_subtype' => ! empty($subtypeName) ? $subtypeName : null,
                            'cost_price' => $costPrice,
                            'selling_price' => $sellingPrice,
                            'barcode' => ! empty($barcode) ? $barcode : null,
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
                    if (! $existingProduct && $productType === 'PHYSICAL' && $initialStock > 0) {
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
                $errors[] = "Baris {$rowNumber}: ".$e->getMessage();
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

    public function importFromExcel(string $filePath, User $user): array
    {
        if (! file_exists($filePath)) {
            throw new InvalidArgumentException("File tidak ditemukan pada path: {$filePath}");
        }

        $reader = new Reader;
        $reader->open($filePath);
        $temporaryCsv = tempnam(sys_get_temp_dir(), 'raja-pos-import-');
        $handle = fopen($temporaryCsv, 'w');
        $hasRows = false;
        $headerFound = false;

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $values = $row->toArray();
                    $normalized = array_map(fn ($value) => strtolower(trim((string) $value)), $values);

                    if (! $headerFound) {
                        if (! in_array('kode produk', $normalized, true) || ! in_array('nama barang/layanan', $normalized, true)) {
                            continue;
                        }

                        $headerFound = true;
                    }

                    fputcsv($handle, $values);
                    $hasRows = true;
                }

                if ($headerFound) {
                    break;
                }
            }
        } finally {
            fclose($handle);
            $reader->close();
        }

        if (! $hasRows) {
            @unlink($temporaryCsv);
            throw new InvalidArgumentException('Worksheet Excel kosong.');
        }

        try {
            return $this->importFromCsv($temporaryCsv, $user);
        } finally {
            @unlink($temporaryCsv);
        }
    }

    public function generateExcelTemplate(): string
    {
        $path = $this->temporaryXlsxPath('Template_Import_Produk_RajaPOS');
        $writer = $this->openFormattedWorkbook($path, 'Template Produk');
        $writer->addRow(Row::fromValues(['RAJA POS - TEMPLATE MASTER PRODUK'], $this->titleStyle()));
        $writer->addRow(Row::fromValues(['Isi data mulai baris berikut. Jenis Stok hanya: PHYSICAL, DIGITAL, atau LAYANAN.'], $this->noteStyle()));
        $writer->addRow(Row::fromValues($this->excelHeaders(), $this->headerStyle()));
        $writer->addRow($this->productDataRow(['RJA-ACOM-0004', 'ACOME Selfie Stick SS07A', 'Aksesoris', 'Tripod', 'ACOME', 'PHYSICAL', 40000, 80000, '8991234567890', 20, 5, '']));
        $writer->addRow($this->productDataRow(['RJA-XL-0001', 'XL Combo Flex Max 7 GB', 'Pulsa', 'Multi', 'XL', 'DIGITAL', 32174, 35000, '', 0, 0, 'MULTI']));
        $writer->addRow($this->productDataRow(['SVC-TG-0001', 'Jasa Pasang Tempered Glass', 'Jasa & Layanan', 'Layanan', 'RAJA', 'LAYANAN', 0, 10000, '', 0, 0, '']));
        $writer->close();

        return $path;
    }

    public function exportProductsToExcel(?string $categoryId = null, ?string $productType = null): string
    {
        $query = Product::with(['category', 'brand', 'defaultBalanceAccount', 'inventories']);
        if (! empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }
        if (! empty($productType) && $productType !== 'ALL') {
            $query->where('product_type', $productType);
        }

        $path = $this->temporaryXlsxPath('Export_Master_Produk_RajaPOS');
        $writer = $this->openFormattedWorkbook($path, 'Master Produk');
        $writer->addRow(Row::fromValues(['RAJA POS - MASTER PRODUK'], $this->titleStyle()));
        $writer->addRow(Row::fromValues(['Diekspor pada '.now()->format('d M Y H:i').' | Stok merupakan total seluruh lokasi.'], $this->noteStyle()));
        $writer->addRow(Row::fromValues([...$this->excelHeaders(), 'Status Harga'], $this->headerStyle()));

        foreach ($query->orderBy('name')->cursor() as $product) {
            $writer->addRow($this->productDataRow([
                $product->code,
                $product->name,
                $product->category?->name ?? 'Umum',
                $product->product_subtype ?? '',
                $product->brand?->name ?? '',
                $product->product_type,
                (float) $product->cost_price,
                (float) $product->selling_price,
                $product->effective_barcode,
                $product->product_type === 'PHYSICAL' ? $product->inventories->sum('quantity') : 0,
                $product->minimum_stock,
                $product->defaultBalanceAccount?->name ?? '',
                $product->price_status === 'COMPLETE' ? 'Lengkap' : 'Harga Belum Lengkap',
            ]));
        }

        $writer->close();

        return $path;
    }

    private function openFormattedWorkbook(string $path, string $sheetName): Writer
    {
        $writer = new Writer;
        $writer->openToFile($path);
        $sheet = $writer->getCurrentSheet();
        $sheet->setName($sheetName);
        $sheet->setColumnWidth(18, 1);
        $sheet->setColumnWidth(36, 2);
        $sheet->setColumnWidth(18, 3, 6);
        $sheet->setColumnWidth(16, 7, 8);
        $sheet->setColumnWidth(20, 9);
        $sheet->setColumnWidth(12, 10, 11);
        $sheet->setColumnWidth(20, 12, 13);

        return $writer;
    }

    private function excelHeaders(): array
    {
        return ['Kode Produk', 'Nama Barang/Layanan', 'Kategori', 'Jenis', 'Merk', 'Jenis Stok', 'Modal', 'Harga Jual', 'Barcode', 'Stok Awal', 'Stok Minimum', 'Provider Akun'];
    }

    private function titleStyle(): Style
    {
        return (new Style)->setFontBold()->setFontSize(14)->setFontColor('FFFFFFFF')->setBackgroundColor('FF3F7A5D');
    }

    private function headerStyle(): Style
    {
        return (new Style)->setFontBold()->setFontColor('FFFFFFFF')->setBackgroundColor('FF3F7A5D')->setShouldWrapText();
    }

    private function noteStyle(): Style
    {
        return (new Style)->setFontColor('FF52655B')->setBackgroundColor('FFE3EEE8')->setShouldWrapText();
    }

    private function dataStyle(): Style
    {
        return (new Style)->setShouldWrapText();
    }

    private function currencyStyle(): Style
    {
        return (new Style)->setFormat('[$Rp-421] #,##0');
    }

    private function productDataRow(array $values): Row
    {
        return Row::fromValuesWithStyles($values, $this->dataStyle(), [6 => $this->currencyStyle(), 7 => $this->currencyStyle()]);
    }

    private function temporaryXlsxPath(string $prefix): string
    {
        $temporaryFile = tempnam(sys_get_temp_dir(), $prefix.'-');
        @unlink($temporaryFile);

        return $temporaryFile.'.xlsx';
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
