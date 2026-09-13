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
        Schema::table('daily_summaries', function (Blueprint $table) {
            $table->string('status', 30)->default('BELUM_DICEK')->after('summary_date');
            $table->boolean('has_discrepancy')->default(false)->after('status');
            $table->timestamp('verified_at')->nullable()->after('notes');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->after('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_summaries', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['status', 'has_discrepancy', 'verified_at', 'verified_by']);
        });
    }
};
