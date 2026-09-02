<?php

namespace App\Livewire\Admin;

use App\Models\Location;
use App\Models\Product;
use App\Models\StockOpname as StockOpnameModel;
use App\Services\InventoryService;
use Livewire\Component;
use Livewire\WithPagination;

class StockOpname extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $location_id = '';
    public $product_id = '';
    public $physical_qty = 0;
    public $notes = '';

    protected $paginationTheme = 'tailwind';

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
        $this->validate([
            'location_id' => 'required',
            'product_id' => 'required',
            'physical_qty' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($this->product_id);
        $systemQty = \App\Models\Inventory::where('product_id', $this->product_id)
            ->where('location_id', $this->location_id)->value('quantity') ?? 0;

        StockOpnameModel::create([
            'opname_number' => 'OPN-' . date('Ymd') . '-' . rand(100, 999),
            'location_id' => $this->location_id,
            'product_id' => $this->product_id,
            'system_quantity' => $systemQty,
            'physical_quantity' => $this->physical_qty,
            'difference' => $this->physical_qty - $systemQty,
            'status' => 'PENDING',
            'notes' => $this->notes,
            'created_by' => auth()->id(),
        ]);

        $this->showCreateModal = false;
        $this->dispatch('notify', message: 'Sesi Stock Opname berhasil dibuat.', type: 'success');
    }

    public function approveSession($id, InventoryService $inventoryService)
    {
        try {
            $opname = StockOpnameModel::findOrFail($id);
            if ($opname->status !== 'PENDING') return;

            $inventoryService->adjustStock(
                $opname->product_id,
                $opname->location_id,
                $opname->physical_quantity,
                'STOCK_OPNAME',
                'Stock Opname Approval: ' . $opname->opname_number,
                auth()->id()
            );

            $opname->update([
                'status' => 'APPROVED',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $this->dispatch('notify', message: 'Sesi Stock Opname berhasil disetujui & stok fisik terupdate.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'danger');
        }
    }

    public function render()
    {
        $sessions = StockOpnameModel::with(['location', 'product', 'creator', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.stock-opname', [
            'sessions' => $sessions,
            'locations' => Location::all(),
            'products' => Product::where('product_type', 'PHYSICAL')->orderBy('name')->get(),
        ])->layout('components.layouts.admin', ['title' => 'Stock Opname - Raja POS']);
    }
}
