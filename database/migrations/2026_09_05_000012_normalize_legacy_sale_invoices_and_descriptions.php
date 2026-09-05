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
        // 1. Normalize sales invoice_number from UUID to short format
        $sales = DB::table('sales')->get();
        foreach ($sales as $sale) {
            $inv = trim((string) ($sale->invoice_number ?? ''));
            if (strlen($inv) > 20 && str_contains($inv, '-')) {
                $parts = array_values(array_filter(explode('-', $inv)));
                $prefix = strtoupper(!empty($parts[0]) ? $parts[0] : 'TRX');
                $code = strtoupper(!empty($parts[1]) ? $parts[1] : substr($inv, -8));
                $newInv = $prefix . '-' . substr($code, 0, 8);

                $baseNew = $newInv;
                $counter = 1;
                while (DB::table('sales')->where('invoice_number', $newInv)->where('id', '!=', $sale->id)->exists()) {
                    $newInv = $baseNew . '-' . $counter;
                    $counter++;
                }

                DB::table('sales')->where('id', $sale->id)->update(['invoice_number' => $newInv]);
            }
        }

        // 2. Normalize descriptions in balance_transactions containing long UUID invoice/transaction numbers
        $transactions = DB::table('balance_transactions')->get();
        foreach ($transactions as $trx) {
            $desc = (string) ($trx->description ?? '');
            if (preg_match('/TRX-[a-f0-9]{8}-[a-f0-9-]{20,}/i', $desc)) {
                $newDesc = preg_replace_callback('/(TRX)-([a-f0-9]{8})-[a-f0-9-]{20,}/i', function ($matches) {
                    return strtoupper($matches[1] . '-' . substr($matches[2], 0, 8));
                }, $desc);

                if ($newDesc !== $desc) {
                    DB::table('balance_transactions')->where('id', $trx->id)->update(['description' => $newDesc]);
                }
            }
        }

        // 3. Normalize notes in inventory_movements containing long UUID invoice numbers
        $movements = DB::table('inventory_movements')->get();
        foreach ($movements as $m) {
            $notes = (string) ($m->notes ?? '');
            if (preg_match('/TRX-[a-f0-9]{8}-[a-f0-9-]{20,}/i', $notes)) {
                $newNotes = preg_replace_callback('/(TRX)-([a-f0-9]{8})-[a-f0-9-]{20,}/i', function ($matches) {
                    return strtoupper($matches[1] . '-' . substr($matches[2], 0, 8));
                }, $notes);

                if ($newNotes !== $notes) {
                    DB::table('inventory_movements')->where('id', $m->id)->update(['notes' => $newNotes]);
                }
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
