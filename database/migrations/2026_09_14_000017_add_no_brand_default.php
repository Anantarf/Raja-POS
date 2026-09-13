<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $brand = DB::table('brands')->where('slug', 'no-brand')->first();

        if ($brand) {
            DB::table('brands')->where('id', $brand->id)->update([
                'name' => 'No Brand',
                'status' => 'ACTIVE',
                'deleted_at' => null,
                'updated_at' => $now,
            ]);
            $brandId = $brand->id;
        } else {
            $brandId = DB::table('brands')->insertGetId([
                'name' => 'No Brand',
                'slug' => 'no-brand',
                'status' => 'ACTIVE',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('products')->whereNull('brand_id')->update([
            'brand_id' => $brandId,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        $brandId = DB::table('brands')->where('slug', 'no-brand')->value('id');

        if (! $brandId) {
            return;
        }

        DB::table('products')->where('brand_id', $brandId)->update(['brand_id' => null]);
        DB::table('brands')->where('id', $brandId)->delete();
    }
};
