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
        Schema::create('daily_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            $table->date('summary_date');

            // DANA E-Wallet
            $table->decimal('dana_saldo_awal', 15, 2)->default(0);
            $table->decimal('dana_topup', 15, 2)->default(0);
            $table->decimal('dana_trx', 15, 2)->default(0);
            $table->decimal('dana_saldo_android', 15, 2)->default(0);

            // QRIS
            $table->decimal('qris_tarik_tunai', 15, 2)->default(0);
            $table->decimal('qris_saldo_android', 15, 2)->default(0);

            // BANK MAS
            $table->decimal('bankmas_saldo_awal', 15, 2)->default(0);
            $table->decimal('bankmas_topup', 15, 2)->default(0);
            $table->decimal('bankmas_trx', 15, 2)->default(0);
            $table->decimal('bankmas_saldo_android', 15, 2)->default(0);

            // MULTI
            $table->decimal('multi_saldo_awal', 15, 2)->default(0);
            $table->decimal('multi_topup', 15, 2)->default(0);
            $table->decimal('multi_trx', 15, 2)->default(0);
            $table->decimal('multi_saldo_android', 15, 2)->default(0);

            // Rekap Kasir
            $table->decimal('tarik_tunai_kasir', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['location_id', 'summary_date'], 'unique_daily_summary_per_location_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_summaries');
    }
};
