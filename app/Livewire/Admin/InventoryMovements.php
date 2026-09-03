<?php

namespace App\Livewire\Admin;

use App\Models\InventoryMovement;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryMovements extends Component
{
    use WithPagination;

    public string $search = '';

    public string $movementType = 'ALL';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingMovementType(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = InventoryMovement::with(['product', 'location', 'creator'])
            ->latest();

        if ($this->movementType !== 'ALL') {
            $query->where('movement_type', $this->movementType);
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('movement_type', 'like', '%'.$this->search.'%')
                    ->orWhere('notes', 'like', '%'.$this->search.'%')
                    ->orWhereHas('product', function ($productQuery) {
                        $productQuery->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('code', 'like', '%'.$this->search.'%');
                    });
            });
        }

        return view('livewire.admin.inventory-movements', [
            'movements' => $query->paginate(12),
            'movementTypes' => InventoryMovement::query()
                ->select('movement_type')
                ->distinct()
                ->orderBy('movement_type')
                ->pluck('movement_type'),
        ])->layout('components.layouts.admin', ['title' => 'Pergerakan Stok - Raja POS']);
    }
}
