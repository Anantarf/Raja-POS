<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class CatalogCacheService
{
    /**
     * Cache TTL in seconds (1 hour default, invalidated automatically on mutation).
     */
    protected const CACHE_TTL = 3600;

    /**
     * Get active product catalog cached by location.
     */
    public function getCachedProductsForPos(?Location $location = null): \Illuminate\Database\Eloquent\Collection
    {
        $locationId = $location?->id ?? 0;
        $cacheKey = "pos_catalog_products_loc_{$locationId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($location) {
            $query = Product::with(['category', 'brand', 'inventories'])
                ->where('status', 'ACTIVE');

            if ($location) {
                $query->where(function ($q) use ($location) {
                    $q->where('product_type', '!=', 'PHYSICAL')
                      ->orWhereHas('inventories', function ($invQ) use ($location) {
                          $invQ->where('location_id', $location->id);
                      });
                });
            }

            return $query->orderBy('name')->get();
        });
    }

    /**
     * Clear catalog cache across all locations or for a specific location.
     */
    public function clearCatalogCache(?int $locationId = null): void
    {
        if ($locationId) {
            Cache::forget("pos_catalog_products_loc_{$locationId}");
        } else {
            // Clear default location 0 and all active locations
            Cache::forget('pos_catalog_products_loc_0');
            foreach (Location::pluck('id') as $id) {
                Cache::forget("pos_catalog_products_loc_{$id}");
            }
        }
    }
}
