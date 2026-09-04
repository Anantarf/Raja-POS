<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Normalize long UUID transaction numbers
        $transactions = DB::table('balance_transactions')->get();
        foreach ($transactions as $trx) {
            $number = trim((string) ($trx->transaction_number ?? ''));
            if (strlen($number) > 20 && str_contains($number, '-')) {
                $parts = array_values(array_filter(explode('-', $number)));
                $prefix = strtoupper(!empty($parts[0]) ? $parts[0] : 'TRX');
                $code = strtoupper(!empty($parts[1]) ? $parts[1] : substr($number, -8));
                $newNumber = $prefix . '-' . substr($code, 0, 8);

                $baseNew = $newNumber;
                $counter = 1;
                while (DB::table('balance_transactions')->where('transaction_number', $newNumber)->where('id', '!=', $trx->id)->exists()) {
                    $newNumber = $baseNew . '-' . $counter;
                    $counter++;
                }

                DB::table('balance_transactions')->where('id', $trx->id)->update(['transaction_number' => $newNumber]);
            }
        }

        // 2. Fix legacy typo in descriptions
        DB::table('balance_transactions')
            ->where('description', 'like', '%Pembalian%')
            ->update([
                'description' => DB::raw("REPLACE(description, 'Pembalian', 'Pengembalian')")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
