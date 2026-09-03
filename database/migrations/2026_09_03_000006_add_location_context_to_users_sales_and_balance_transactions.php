<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('role_id')->constrained('locations')->nullOnDelete();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('cashier_id')->constrained('locations')->nullOnDelete();
        });

        Schema::table('balance_transactions', function (Blueprint $table) {
            $table->decimal('destination_balance_before', 15, 2)->nullable()->after('balance_after');
            $table->decimal('destination_balance_after', 15, 2)->nullable()->after('destination_balance_before');
        });
    }

    public function down(): void
    {
        Schema::table('balance_transactions', function (Blueprint $table) {
            $table->dropColumn(['destination_balance_before', 'destination_balance_after']);
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
        });
    }
};
