<?php

use App\Models\BalanceTransaction;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $transactions = BalanceTransaction::all();
        foreach ($transactions as $trx) {
            $number = trim((string) ($trx->transaction_number ?? ''));
            if (strlen($number) > 20 && str_contains($number, '-')) {
                $parts = array_values(array_filter(explode('-', $number)));
                $prefix = strtoupper(!empty($parts[0]) ? $parts[0] : 'TRX');
                $code = strtoupper(!empty($parts[1]) ? $parts[1] : substr($number, -8));
                $newNumber = $prefix . '-' . substr($code, 0, 8);

                $baseNew = $newNumber;
                $counter = 1;
                while (BalanceTransaction::where('transaction_number', $newNumber)->where('id', '!=', $trx->id)->exists()) {
                    $newNumber = $baseNew . '-' . $counter;
                    $counter++;
                }

                $trx->update(['transaction_number' => $newNumber]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
