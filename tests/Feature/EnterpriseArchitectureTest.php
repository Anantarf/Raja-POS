<?php

namespace Tests\Feature;

use App\Jobs\ProcessAuditLogJob;
use App\Jobs\RecordInventoryMovementJob;
use App\Models\Location;
use App\Models\Product;
use App\Models\User;
use App\Services\CatalogCacheService;
use App\Services\InventoryService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EnterpriseArchitectureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_catalog_caching_and_automatic_invalidation(): void
    {
        $location = Location::where('code', 'RAJA-BANGO')->first();
        $cacheService = app(CatalogCacheService::class);
        $inventoryService = app(InventoryService::class);

        // 1. Initial cached catalog count
        $catalogInitial = $cacheService->getCachedProductsForPos($location);
        $initialCount = $catalogInitial->count();

        // 2. Create a new product and adjust stock
        $product = Product::create([
            'code' => 'ENT-001',
            'name' => 'Enterprise Cable 100W',
            'product_type' => 'PHYSICAL',
            'cost_price' => 20000,
            'selling_price' => 50000,
            'status' => 'ACTIVE',
        ]);

        $owner = User::where('username', 'superadmin')->first();
        $inventoryService->adjustStock($product, $location, 50, 'ADJUSTMENT_IN', 'Initial stock', $owner);

        // 3. Fetch catalog again, should get fresh invalidated cache with new product
        $catalogUpdated = $cacheService->getCachedProductsForPos($location);
        $this->assertEquals($initialCount + 1, $catalogUpdated->count());
        $this->assertTrue($catalogUpdated->pluck('code')->contains('ENT-001'));
    }

    public function test_asynchronous_queue_job_dispatching(): void
    {
        Queue::fake();

        // Dispatch RecordInventoryMovementJob
        RecordInventoryMovementJob::dispatch(
            productId: 1,
            locationId: 1,
            movementType: 'SALE',
            quantityBefore: 10,
            quantityChange: -2,
            quantityAfter: 8,
            notes: 'Test sale'
        );

        Queue::assertPushed(RecordInventoryMovementJob::class, function ($job) {
            return $job->productId === 1 && $job->movementType === 'SALE';
        });

        // Dispatch ProcessAuditLogJob
        ProcessAuditLogJob::dispatch(
            action: 'POS_CHECKOUT',
            description: 'Processed test checkout #1001',
            userId: 1
        );

        Queue::assertPushed(ProcessAuditLogJob::class, function ($job) {
            return $job->action === 'POS_CHECKOUT';
        });
    }
}
