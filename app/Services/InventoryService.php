<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockOpname;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InventoryService
{
    /**
     * Adjust stock for a physical product at a specific location.
     * Uses row locking (SELECT ... FOR UPDATE) and DB Transaction.
     */
    public function adjustStock(
        Product $product,
        Location $location,
        int $quantityChange,
        string $movementType,
        ?string $notes = null,
        ?User $user = null,
        mixed $reference = null
    ): ?InventoryMovement {
        // Digital and Service products do not keep physical stock quantity (PRD Bab 10)
        if ($product->product_type !== 'PHYSICAL') {
            return null;
        }

        return DB::transaction(function () use ($product, $location, $quantityChange, $movementType, $notes, $user, $reference) {
            // Row lock inventory record
            $inventory = Inventory::where('product_id', $product->id)
                ->where('location_id', $location->id)
                ->lockForUpdate()
                ->first();

            if (! $inventory) {
                $inventory = Inventory::create([
                    'product_id' => $product->id,
                    'location_id' => $location->id,
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                ]);
            }

            $quantityBefore = $inventory->quantity;
            $quantityAfter = $quantityBefore + $quantityChange;

            if ($quantityAfter < 0) {
                throw new InvalidArgumentException("Stok produk '{$product->name}' tidak mencukupi.");
            }

            // Update inventory
            $inventory->update([
                'quantity' => $quantityAfter,
                'last_stock_at' => now(),
            ]);

            // Create movement log
            return InventoryMovement::create([
                'product_id' => $product->id,
                'location_id' => $location->id,
                'movement_type' => $movementType,
                'quantity_before' => $quantityBefore,
                'quantity_change' => $quantityChange,
                'quantity_after' => $quantityAfter,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference ? $reference->id : null,
                'notes' => $notes,
                'created_by' => $user?->id ?? auth()->id(),
            ]);
        });
    }

    /**
     * Approve a stock opname session and apply stock movements for differences.
     */
    public function approveStockOpname(StockOpname $stockOpname, User $approver): bool
    {
        if ($stockOpname->status === 'COMPLETED') {
            throw new InvalidArgumentException('Stock Opname sudah disetujui sebelumnya.');
        }

        return DB::transaction(function () use ($stockOpname, $approver) {
            $location = $stockOpname->location;

            foreach ($stockOpname->items as $item) {
                $currentQuantity = (int) (Inventory::where('product_id', $item->product_id)
                    ->where('location_id', $location->id)
                    ->lockForUpdate()
                    ->value('quantity') ?? 0);
                $quantityChange = $item->physical_quantity - $currentQuantity;

                $item->update([
                    'system_quantity' => $currentQuantity,
                    'difference' => $quantityChange,
                ]);

                if ($quantityChange !== 0) {
                    $this->adjustStock(
                        product: $item->product,
                        location: $location,
                        quantityChange: $quantityChange,
                        movementType: 'STOCK_OPNAME',
                        notes: 'Penyesuaian Opname #'.$stockOpname->opname_number.($item->notes ? ' - '.$item->notes : ''),
                        user: $approver,
                        reference: $stockOpname
                    );
                }
            }

            $stockOpname->update([
                'status' => 'COMPLETED',
                'approved_by' => $approver->id,
                'completed_at' => now(),
            ]);

            return true;
        });
    }
}
