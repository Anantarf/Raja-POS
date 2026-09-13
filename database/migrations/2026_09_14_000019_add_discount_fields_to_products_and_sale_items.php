<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('discount_type', 20)->default('NONE')->after('selling_price');
            $table->decimal('discount_value', 12, 2)->default(0)->after('discount_type');
            $table->boolean('is_discount_active')->default(true)->after('discount_value');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('original_unit_price', 12, 2)->nullable()->after('quantity');
            $table->decimal('unit_discount', 12, 2)->default(0)->after('original_unit_price');
            $table->decimal('total_discount', 12, 2)->default(0)->after('unit_discount');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value', 'is_discount_active']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['original_unit_price', 'unit_discount', 'total_discount']);
        });
    }
};
