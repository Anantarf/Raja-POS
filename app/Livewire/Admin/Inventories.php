<?php

namespace App\Livewire\Admin;

use App\Models\Inventory;
use App\Models\Location;
use App\Services\InventoryService;
use Livewire\Component;
use Livewire\WithPagination;

class Inventories extends Component
{
    use WithPagination;

    public $search = '';

    public $selectedLocationId = null;

    // Adjustment Modal
    public $showAdjustmentModal = false;

    public $adjustInventoryId = null;

    public $adjustQuantity = 0;

    public $adjustType = 'SET'; // ADD, SUBTRACT, SET

    public $adjustReason = 'Penyesuaian manual stok admin';

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->selectedLocationId = Location::first()?->id;
    }

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

    public function render()
    {
        $query = Inventory::with(['product.category', 'location']);

        if ($this->selectedLocationId) {
            $query->where('location_id', $this->selectedLocationId);
        }

        if ($this->search) {
            $query->whereHas('product', function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%')
                    ->orWhere('barcode', 'like', '%'.$this->search.'%');
            });
        }

        $inventories = $query->paginate(12);

        return view('livewire.admin.inventories', [
            'inventories' => $inventories,
            'locations' => Location::all(),
        ])->layout('components.layouts.admin', ['title' => 'Stok Fisik - Raja POS']);
    }
}
