<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')->where('product_type', 'SERVICE')->update(['product_type' => 'LAYANAN']);

        $defaultLocationId = DB::table('locations')->orderBy('id')->value('id');
        if (! $defaultLocationId) {
            return;
        }

        DB::table('users')->whereNull('location_id')->update(['location_id' => $defaultLocationId]);
        DB::table('sales')->whereNull('location_id')->update(['location_id' => $defaultLocationId]);
    }

    public function down(): void
    {
        // Data normalization is intentionally irreversible.
    }
};
