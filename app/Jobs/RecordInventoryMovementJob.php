<?php

namespace App\Jobs;

use App\Models\InventoryMovement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecordInventoryMovementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $productId,
        public int $locationId,
        public string $movementType,
        public int $quantityBefore,
        public int $quantityChange,
        public int $quantityAfter,
        public ?string $notes = null,
        public ?int $userId = null,
        public ?string $referenceType = null,
        public ?int $referenceId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        InventoryMovement::create([
            'product_id' => $this->productId,
            'location_id' => $this->locationId,
            'movement_type' => $this->movementType,
            'quantity_before' => $this->quantityBefore,
            'quantity_change' => $this->quantityChange,
            'quantity_after' => $this->quantityAfter,
            'reference_type' => $this->referenceType,
            'reference_id' => $this->referenceId,
            'notes' => $this->notes,
            'created_by' => $this->userId,
        ]);
    }
}
