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
            // BANK BCA
            $table->decimal('bca_saldo_awal', 15, 2)->default(0)->after('bankmas_saldo_android');
            $table->decimal('bca_topup', 15, 2)->default(0)->after('bca_saldo_awal');
            $table->decimal('bca_trx', 15, 2)->default(0)->after('bca_topup');
            $table->decimal('bca_saldo_android', 15, 2)->default(0)->after('bca_trx');

            // WAHANA
            $table->decimal('wahana_saldo_awal', 15, 2)->default(0)->after('multi_saldo_android');
            $table->decimal('wahana_topup', 15, 2)->default(0)->after('wahana_saldo_awal');
            $table->decimal('wahana_trx', 15, 2)->default(0)->after('wahana_topup');
            $table->decimal('wahana_saldo_android', 15, 2)->default(0)->after('wahana_trx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_summaries', function (Blueprint $table) {
            $table->dropColumn([
                'bca_saldo_awal',
                'bca_topup',
                'bca_trx',
                'bca_saldo_android',
                'wahana_saldo_awal',
                'wahana_topup',
                'wahana_trx',
                'wahana_saldo_android',
            ]);
        });
    }
};
