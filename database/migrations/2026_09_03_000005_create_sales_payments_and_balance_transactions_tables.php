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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('cashier_id')->constrained('users');
            $table->dateTime('transaction_date');

            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('amount_paid', 15, 2);
            $table->decimal('change_amount', 15, 2)->default(0);

            $table->text('trash_reason')->nullable();
            $table->foreignId('trashed_by')->nullable()->constrained('users');
            $table->timestamp('trashed_at')->nullable();

            $table->foreignId('restored_by')->nullable()->constrained('users');
            $table->timestamp('restored_at')->nullable();

            $table->decimal('total_cost', 15, 2)->default(0);
            $table->decimal('gross_profit', 15, 2)->default(0);

            $table->string('status')->default('COMPLETED'); // DRAFT, COMPLETED, TRASHED, DELETED
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('transaction_date');
            $table->index('status');
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();

            $table->string('product_name_snapshot');
            $table->string('product_code_snapshot');
            $table->string('product_type_snapshot');
            $table->string('product_subtype_snapshot')->nullable();
            $table->string('modal_account_snapshot')->nullable();

            $table->integer('quantity');
            $table->decimal('cost_price', 15, 2);
            $table->decimal('selling_price', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2);

            $table->timestamps();

            $table->index('sale_id');
            $table->index('product_id');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('payment_method_id')->constrained('payment_methods');
            $table->foreignId('balance_account_id')->nullable()->constrained('balance_accounts');

            $table->decimal('amount', 15, 2);
            $table->decimal('change_amount', 15, 2)->default(0);
            $table->string('reference_number')->nullable();
            $table->string('status')->default('COMPLETED');
            $table->dateTime('paid_at');

            $table->timestamps();

            $table->index('sale_id');
        });

        Schema::create('balance_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->string('transaction_type'); // DEPOSIT, WITHDRAWAL, TRANSFER, SALE_RECEIPT, DIGITAL_COST, ADJUSTMENT, EXPENSE, TRASH_REVERSAL, RESTORE_REVERSAL

            $table->foreignId('source_account_id')->nullable()->constrained('balance_accounts');
            $table->foreignId('destination_account_id')->nullable()->constrained('balance_accounts');

            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2)->nullable();
            $table->decimal('balance_after', 15, 2)->nullable();

            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->dateTime('transaction_date');

            $table->timestamps();

            $table->index('transaction_date');
            $table->index('transaction_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_transactions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
    }
};
