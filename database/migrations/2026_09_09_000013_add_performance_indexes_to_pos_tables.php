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
        Schema::table('sales', function (Blueprint $table) {
            $table->index(['location_id', 'created_at'], 'idx_sales_location_created');
            $table->index(['cashier_id', 'created_at'], 'idx_sales_cashier_created');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->index(['location_id', 'product_id'], 'idx_inventories_location_product');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('barcode', 'idx_products_barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex('idx_sales_location_created');
            $table->dropIndex('idx_sales_cashier_created');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex('idx_inventories_location_product');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_barcode');
        });
    }
};
