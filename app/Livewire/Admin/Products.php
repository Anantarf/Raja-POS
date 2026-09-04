<?php

namespace App\Livewire\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Product;
use App\Services\InventoryService;
use App\Services\ProductImportService;
use App\Traits\WithSorting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Products extends Component
{
    use WithPagination, WithFileUploads, WithSorting;

    public $search = '';

    public $selectedCategory = null;

    public $selectedType = 'ALL';

    public $viewMode = 'table'; // 'card' or 'table'

    // Modal state
    public $showCreateModal = false;

    public $showImportModal = false;

    public $editingProductId = null;

    // Form Fields
    public $code = '';

    public $barcode = '';

    public $name = '';

    public $category_id = '';

    public $brand_id = '';

    public $product_type = 'PHYSICAL';

    public $product_subtype = '';

    public $cost_price = 0;

    public $selling_price = 0;

    public $description = '';

    public $initial_stock = 0;

    // Import file
    public $importFile = null;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory()
    {
        $this->resetPage();
    }

    public function updatingSelectedType()
    {
        $this->resetPage();
    }

    public function filterType($type)
    {
        $this->selectedType = $type;
        $this->resetPage();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function generateAutoCode(): void
    {
        $lastProduct = Product::where('code', 'LIKE', 'RJA-%')->orderBy('id', 'desc')->first();
        $nextNum = 1200;

        if ($lastProduct && preg_match('/(\d+)$/', $lastProduct->code, $matches)) {
            $nextNum = (int) $matches[1] + 1;
        }

        $prefix = 'RJA-PROD';
        if ($this->brand_id) {
            $brand = Brand::find($this->brand_id);
            if ($brand && ! empty($brand->name)) {
                $cleanBrand = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $brand->name));
                $prefix = 'RJA-'.substr($cleanBrand, 0, 4);
            }
        }

        $this->code = sprintf('%s-%04d', $prefix, $nextNum);
    }

    public function updatedBrandId()
    {
        if (! $this->editingProductId && empty($this->code)) {
            $this->generateAutoCode();
        }
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->generateAutoCode();
        $this->showCreateModal = true;
    }

    public function openEditModal($productId)
    {
        $product = Product::findOrFail($productId);
        $this->editingProductId = $product->id;
        $this->code = $product->code;
        $this->barcode = $product->barcode;
        $this->name = $product->name;
        $this->category_id = $product->category_id;
        $this->brand_id = $product->brand_id;
        $this->product_type = $product->product_type;
        $this->product_subtype = $product->product_subtype;
        $this->cost_price = $product->cost_price;
        $this->selling_price = $product->selling_price;
        $this->description = $product->description;

        $inv = Inventory::where('product_id', $product->id)->first();
        $this->initial_stock = $inv?->quantity ?? 0;

        $this->showCreateModal = true;
    }

    public function saveProduct(InventoryService $inventoryService)
    {
        abort_unless(auth()->user()->can($this->editingProductId ? 'product.update' : 'product.create'), 403);
        $this->validate([
            'code' => 'required|string|max:50|unique:products,code,'.$this->editingProductId,
            'name' => 'required|string|max:255',
            'product_type' => 'required|in:PHYSICAL,DIGITAL,LAYANAN',
            'product_subtype' => 'nullable|string|max:255',
            'selling_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
        ]);

        $priceStatus = ($this->product_type === 'LAYANAN' || ($this->cost_price > 0 && $this->selling_price > 0)) ? 'COMPLETE' : 'INCOMPLETE';

        $data = [
            'code' => $this->code,
            'barcode' => $this->barcode ?: null,
            'name' => $this->name,
            'category_id' => $this->category_id ?: null,
            'brand_id' => $this->brand_id ?: null,
            'product_type' => $this->product_type,
            'product_subtype' => $this->product_subtype ?: null,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'price_status' => $priceStatus,
            'description' => $this->description,
        ];

        if ($this->editingProductId) {
            $product = Product::findOrFail($this->editingProductId);
            $product->update($data);
            $message = 'Produk berhasil diperbarui.';
        } else {
            $product = Product::create($data);

            if ($product->product_type === 'PHYSICAL') {
                $location = auth()->user()?->location ?? Location::first();
                if ($location) {
                    if ($this->initial_stock > 0) {
                        $inventoryService->adjustStock(
                            product: $product,
                            location: $location,
                            quantityChange: (int) $this->initial_stock,
                            movementType: 'ADJUSTMENT_IN',
                            notes: 'Stok awal produk baru',
                            user: auth()->user(),
                            reference: $product
                        );
                    }
                }
            }

            $message = 'Produk baru berhasil ditambahkan.';
        }

        $this->showCreateModal = false;
        $this->resetForm();
        app(\App\Services\CatalogCacheService::class)->clearCatalogCache();
        $this->dispatch('notify', message: $message, type: 'success');
    }

    public function processImport(ProductImportService $importService)
    {
        abort_unless(auth()->user()->can('excel_import.commit'), 403);
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx|max:10240',
        ]);

        $path = $this->importFile->getRealPath();
        $result = $importService->importFromExcel($path, auth()->user());

        $this->showImportModal = false;
        $this->importFile = null;
        app(\App\Services\CatalogCacheService::class)->clearCatalogCache();

        $msg = "Import Selesai: {$result['imported_count']} produk sukses ditambahkan.";
        if (count($result['errors']) > 0) {
            $msg .= ' ('.count($result['errors']).' baris gagal)';
        }

        $this->dispatch('notify', message: $msg, type: count($result['errors']) > 0 ? 'warning' : 'success');
    }

    public function deleteProduct($productId)
    {
        abort_unless(auth()->user()->can('product.delete'), 403);
        $product = Product::findOrFail($productId);
        $product->delete();
        app(\App\Services\CatalogCacheService::class)->clearCatalogCache();
        $this->dispatch('notify', message: 'Produk berhasil dihapus.', type: 'danger');
    }

    private function resetForm()
    {
        $this->editingProductId = null;
        $this->code = 'PRD-'.rand(1000, 9999);
        $this->barcode = '';
        $this->name = '';
        $this->category_id = '';
        $this->brand_id = '';
        $this->product_type = 'PHYSICAL';
        $this->product_subtype = '';
        $this->cost_price = 0;
        $this->selling_price = 0;
        $this->description = '';
        $this->initial_stock = 0;
    }

    public function render()
    {
        $query = Product::with(['category', 'brand', 'inventories']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%')
                    ->orWhere('barcode', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        if ($this->selectedType === 'INCOMPLETE') {
            $query->where('product_type', '!=', 'LAYANAN')
                ->where(function ($q) {
                    $q->where('price_status', 'INCOMPLETE')
                        ->orWhere('cost_price', '<=', 0)
                        ->orWhere('selling_price', '<=', 0);
                });
        } elseif ($this->selectedType !== 'ALL') {
            $query->where('product_type', $this->selectedType);
        }

        $incompleteCount = Product::where('product_type', '!=', 'LAYANAN')
            ->where(function ($q) {
                $q->where('price_status', 'INCOMPLETE')
                    ->orWhere('cost_price', '<=', 0)
                    ->orWhere('selling_price', '<=', 0);
            })
            ->count();

        $direction = in_array($this->sortDirection, ['asc', 'desc']) ? $this->sortDirection : 'desc';

        if ($this->sortField === 'stock' || $this->sortField === 'quantity') {
            $query->withSum('inventories', 'quantity')
                ->orderBy('inventories_sum_quantity', $direction);
        } else {
            $allowedSorts = ['name', 'code', 'cost_price', 'selling_price', 'product_type', 'created_at'];
            $field = in_array($this->sortField, $allowedSorts) ? $this->sortField : 'created_at';
            $query->orderBy($field, $direction);
        }

        $products = $query->paginate(12);

        return view('livewire.admin.products', [
            'products' => $products,
            'incompleteCount' => $incompleteCount,
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
        ])->layout('components.layouts.admin', ['title' => 'Master Produk']);
    }
}
