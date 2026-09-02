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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('address')->nullable();
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('type'); // CASH, QRIS, TRANSFER, E_WALLET
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
        });

        Schema::create('balance_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('account_type'); // CASH, QRIS, BANK, E_WALLET, PROVIDER
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('balance_accounts');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('locations');
    }
};
