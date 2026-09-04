<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('balance_accounts', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('account_type')->constrained('locations')->nullOnDelete();
        });

        $firstLocationId = \Illuminate\Support\Facades\DB::table('locations')->value('id');
        if ($firstLocationId) {
            \Illuminate\Support\Facades\DB::table('balance_accounts')
                ->whereNull('location_id')
                ->update(['location_id' => $firstLocationId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balance_accounts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
        });
    }
};
