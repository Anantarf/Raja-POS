<?php

namespace App\Livewire\Admin;

use App\Models\InventoryMovement;
use App\Traits\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryMovements extends Component
{
    use WithPagination, WithSorting;

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
        $query = InventoryMovement::with(['product', 'location', 'creator']);

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

        $allowedSorts = ['movement_type', 'quantity_change', 'created_at'];
        $field = in_array($this->sortField, $allowedSorts) ? $this->sortField : 'created_at';
        $direction = in_array($this->sortDirection, ['asc', 'desc']) ? $this->sortDirection : 'desc';

        return view('livewire.admin.inventory-movements', [
            'movements' => $query->orderBy($field, $direction)->paginate(12),
            'movementTypes' => InventoryMovement::query()
                ->select('movement_type')
                ->distinct()
                ->orderBy('movement_type')
                ->pluck('movement_type'),
        ])->layout('components.layouts.admin', ['title' => 'Pergerakan Stok']);
    }
}
