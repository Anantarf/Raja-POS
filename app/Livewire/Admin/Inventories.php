<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockOpname as StockOpnameModel;
use App\Services\InventoryService;
use App\Traits\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;

class Inventories extends Component
{
    use WithPagination, WithSorting;

    public $activeTab = 'stok'; // 'stok' or 'opname'

    // Tab 1: Stok Fisik
    public $search = '';

    public $selectedLocationId = null;

    // Adjustment Modal (Tab 1)
    public $showAdjustmentModal = false;

    public $adjustInventoryId = null;

    public $adjustQuantity = 0;

    public $adjustType = 'SET'; // ADD, SUBTRACT, SET

    public $adjustReason = 'Penyesuaian manual stok admin';

    // Tab 2: Stock Opname
    public $opnameSearch = '';

    public $showCreateModal = false;

    public $showBulkModal = false;

    public $showDetailModal = false;

    public $selectedOpnameDetail = null;

    // Single Opname Modal (Tab 2)
    public $location_id = '';

    public $product_id = '';

    public $physical_qty = 0;

    public $notes = '';

    // Bulk Opname Sheet Modal (Tab 2)
    public $bulk_location_id = '';

    public $bulk_category_id = '';

    public $bulk_search = '';

    public $bulkItems = [];

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->sortField = 'quantity';
        $this->sortDirection = 'asc';
        $this->selectedLocationId = Location::first()?->id;
        if (request()->query('tab') === 'opname') {
            $this->activeTab = 'opname';
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingOpnameSearch()
    {
        $this->resetPage();
    }

    // --- Tab 1: Stok Fisik Methods ---
    public function openAdjustmentModal($inventoryId)
    {
        $inv = Inventory::findOrFail($inventoryId);
        $this->adjustInventoryId = $inv->id;
        $this->adjustQuantity = $inv->quantity;
        $this->adjustType = 'SET';
        $this->adjustReason = 'Penyesuaian manual stok admin';
        $this->showAdjustmentModal = true;
    }

    public function processAdjustment(InventoryService $inventoryService)
    {
        abort_unless(auth()->user()->can('inventory.adjust'), 403);
        $this->validate([
            'adjustQuantity' => 'required|integer|min:0',
            'adjustReason' => 'required|string|max:255',
        ]);

        try {
            $inv = Inventory::with(['product', 'location'])->findOrFail($this->adjustInventoryId);
            $quantityChange = $this->adjustQuantity - $inv->quantity;
            $movementType = $quantityChange >= 0 ? 'ADJUSTMENT_IN' : 'ADJUSTMENT_OUT';

            if ($this->adjustType === 'ADD') {
                $quantityChange = $this->adjustQuantity;
                $movementType = 'ADJUSTMENT_IN';
            } elseif ($this->adjustType === 'SUBTRACT') {
                $quantityChange = -$this->adjustQuantity;
                $movementType = 'ADJUSTMENT_OUT';
            }

            $inventoryService->adjustStock(
                $inv->product,
                $inv->location,
                $quantityChange,
                $movementType,
                $this->adjustReason,
                auth()->user()
            );

            $this->showAdjustmentModal = false;
            $this->dispatch('notify', message: 'Stok fisik berhasil diperbarui.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'danger');
        }
    }

    // --- Tab 2: Stock Opname Methods ---
    public function openCreateModal()
    {
        $this->location_id = Location::first()?->id;
        $this->product_id = Product::first()?->id;
        $this->physical_qty = 0;
        $this->notes = 'Stock Opname Rutin';
        $this->showCreateModal = true;
    }

    public function createSession()
    {
        abort_unless(auth()->user()->can('stock_opname.create'), 403);
        $this->validate([
            'location_id' => 'required',
            'product_id' => 'required',
            'physical_qty' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($this->product_id);
        $systemQty = Inventory::where('product_id', $this->product_id)
            ->where('location_id', $this->location_id)->value('quantity') ?? 0;

        $opname = StockOpnameModel::create([
            'opname_number' => 'OPN-'.date('YmdHis').'-'.random_int(100, 999),
            'location_id' => $this->location_id,
            'status' => 'DRAFT',
            'created_by' => auth()->id(),
            'started_at' => now(),
        ]);

        $opname->items()->create([
            'product_id' => $product->id,
            'system_quantity' => $systemQty,
            'physical_quantity' => $this->physical_qty,
            'difference' => $this->physical_qty - $systemQty,
            'notes' => $this->notes,
        ]);

        $this->showCreateModal = false;
        $this->dispatch('notify', message: 'Sesi Stock Opname 1 barang berhasil dibuat.', type: 'success');
    }

    public function openBulkModal()
    {
        $this->bulk_location_id = Location::first()?->id;
        $this->bulk_category_id = '';
        $this->bulk_search = '';
        $this->loadBulkItems();
        $this->showBulkModal = true;
    }

    public function loadBulkItems()
    {
        if (! $this->bulk_location_id) {
            return;
        }

        $products = Product::where('product_type', 'PHYSICAL')
            ->when($this->bulk_category_id, function ($q) {
                $q->where('category_id', $this->bulk_category_id);
            })
            ->when($this->bulk_search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%'.$this->bulk_search.'%')
                        ->orWhere('barcode', 'like', '%'.$this->bulk_search.'%')
                        ->orWhere('code', 'like', '%'.$this->bulk_search.'%');
                });
            })
            ->orderBy('name')
            ->get();

        $inventoryMap = Inventory::where('location_id', $this->bulk_location_id)
            ->whereIn('product_id', $products->pluck('id'))
            ->pluck('quantity', 'product_id');

        $this->bulkItems = [];
        foreach ($products as $prod) {
            $sysQty = (int) ($inventoryMap[$prod->id] ?? 0);
            $this->bulkItems[$prod->id] = [
                'product_name' => $prod->name,
                'effective_barcode' => $prod->effective_barcode,
                'system_qty' => $sysQty,
                'physical_qty' => $sysQty,
                'notes' => '',
            ];
        }
    }

    public function updatedBulkLocationId()
    {
        $this->loadBulkItems();
    }

    public function updatedBulkCategoryId()
    {
        $this->loadBulkItems();
    }

    public function updatedBulkSearch()
    {
        $this->loadBulkItems();
    }

    public function createBulkSession()
    {
        abort_unless(auth()->user()->can('stock_opname.create'), 403);
        if (empty($this->bulkItems) || ! $this->bulk_location_id) {
            $this->dispatch('notify', message: 'Tidak ada data barang yang diaudit.', type: 'warning');

            return;
        }

        $opname = StockOpnameModel::create([
            'opname_number' => 'OPN-BULK-'.date('YmdHis').'-'.random_int(100, 999),
            'location_id' => $this->bulk_location_id,
            'status' => 'DRAFT',
            'created_by' => auth()->id(),
            'started_at' => now(),
        ]);

        $itemCount = 0;
        foreach ($this->bulkItems as $productId => $item) {
            $physicalQty = max(0, (int) ($item['physical_qty'] ?? 0));
            $systemQty = (int) ($item['system_qty'] ?? 0);
            $diff = $physicalQty - $systemQty;

            $opname->items()->create([
                'product_id' => $productId,
                'system_quantity' => $systemQty,
                'physical_quantity' => $physicalQty,
                'difference' => $diff,
                'notes' => $item['notes'] ?? 'Audit Masal Toko',
            ]);
            $itemCount++;
        }

        $this->showBulkModal = false;
        $this->dispatch('notify', message: "Sesi Stock Opname Masal ({$itemCount} barang) berhasil dibuat.", type: 'success');
    }

    public function openDetailModal($id)
    {
        $this->selectedOpnameDetail = StockOpnameModel::with(['location', 'items.product', 'creator', 'approver'])->find($id);
        if ($this->selectedOpnameDetail) {
            $this->showDetailModal = true;
        }
    }

    public function approveSession($id, InventoryService $inventoryService)
    {
        abort_unless(auth()->user()->can('stock_opname.approve'), 403);
        try {
            $opname = StockOpnameModel::findOrFail($id);
            if ($opname->status !== 'DRAFT') {
                return;
            }

            $inventoryService->approveStockOpname($opname, auth()->user());

            $this->dispatch('notify', message: 'Sesi Stock Opname berhasil disetujui & stok fisik terupdate.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'danger');
        }
    }

    public function render()
    {
        // Query Tab 1: Inventories
        $inventoryQuery = Inventory::query()
            ->select('inventories.*')
            ->with(['product.category', 'location']);

        if ($this->selectedLocationId) {
            $inventoryQuery->where('inventories.location_id', $this->selectedLocationId);
        }

        if ($this->search) {
            $inventoryQuery->whereHas('product', function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%')
                    ->orWhere('barcode', 'like', '%'.$this->search.'%');
            });
        }

        $direction = in_array($this->sortDirection, ['asc', 'desc']) ? $this->sortDirection : 'asc';

        if ($this->sortField === 'product_name') {
            $inventoryQuery->join('products', 'inventories.product_id', '=', 'products.id')
                ->orderBy('products.name', $direction);
        } elseif ($this->sortField === 'stock_status' || $this->sortField === 'quantity') {
            $inventoryQuery->orderBy('inventories.quantity', $direction);
        } elseif (in_array($this->sortField, ['updated_at', 'created_at'])) {
            $inventoryQuery->orderBy('inventories.'.$this->sortField, $direction);
        } else {
            $inventoryQuery->orderBy('inventories.quantity', 'asc');
        }

        $inventories = $inventoryQuery->paginate(12);

        // Query Tab 2: Stock Opnames
        $opnameQuery = StockOpnameModel::with(['location', 'items.product', 'creator', 'approver'])
            ->withCount('items')
            ->when($this->opnameSearch, function ($query) {
                $query->where('opname_number', 'like', '%'.$this->opnameSearch.'%')
                    ->orWhereHas('items.product', function ($q) {
                        $q->where('name', 'like', '%'.$this->opnameSearch.'%')
                            ->orWhere('barcode', 'like', '%'.$this->opnameSearch.'%')
                            ->orWhere('code', 'like', '%'.$this->opnameSearch.'%');
                    });
            })
            ->orderBy('created_at', 'desc');
        $opnames = $opnameQuery->paginate(10, ['*'], 'opnamePage');

        return view('livewire.admin.inventories', [
            'inventories' => $inventories,
            'opnames' => $opnames,
            'locations' => Location::all(),
            'categories' => Category::orderBy('name')->get(),
            'products' => Product::where('product_type', 'PHYSICAL')->orderBy('name')->get(),
        ])->layout('components.layouts.admin', ['title' => 'Stok & Opname']);
    }
}
