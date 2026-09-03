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

class StockOpname extends Component
{
    use WithPagination, WithSorting;

    public $showCreateModal = false;

    public $showBulkModal = false;

    public $showDetailModal = false;

    public $selectedOpnameDetail = null;

    public $location_id = '';

    public $product_id = '';

    public $physical_qty = 0;

    public $notes = '';

    public $bulk_location_id = '';

    public $bulk_category_id = '';

    public $bulk_search = '';

    public $bulkItems = [];

    public $search = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

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
        $allowedSorts = ['opname_number', 'status', 'created_at'];
        $field = in_array($this->sortField, $allowedSorts) ? $this->sortField : 'created_at';
        $direction = in_array($this->sortDirection, ['asc', 'desc']) ? $this->sortDirection : 'desc';

        $sessions = StockOpnameModel::with(['location', 'items.product', 'creator', 'approver'])
            ->withCount('items')
            ->when($this->search, function ($query) {
                $query->where('opname_number', 'like', '%'.$this->search.'%')
                    ->orWhereHas('items.product', function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('barcode', 'like', '%'.$this->search.'%')
                            ->orWhere('code', 'like', '%'.$this->search.'%');
                    });
            })
            ->orderBy($field, $direction)
            ->paginate(10);

        return view('livewire.admin.stock-opname', [
            'sessions' => $sessions,
            'locations' => Location::all(),
            'categories' => Category::orderBy('name')->get(),
            'products' => Product::where('product_type', 'PHYSICAL')->orderBy('name')->get(),
        ])->layout('components.layouts.admin', ['title' => 'Stock Opname']);
    }
}
