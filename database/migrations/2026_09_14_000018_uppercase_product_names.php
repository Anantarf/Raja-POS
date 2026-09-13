<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')->update([
            'name' => DB::raw('UPPER(name)'),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Original casing cannot be recovered reliably.
    }
};
