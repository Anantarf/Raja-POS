<?php

namespace App\Services;

use App\Models\BalanceAccount;
use App\Models\BalanceTransaction;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class SaleCancellationService
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * Move a COMPLETED sale to Sampah Transaksi (TRASHED).
     * Reverts physical inventory stock and balance account transactions atomically.
     */
    public function moveToTrash(Sale $sale, User $user, string $reason): bool
    {
        if ($sale->status !== 'COMPLETED') {
            throw new InvalidArgumentException('Hanya transaksi berstatus COMPLETED yang dapat dipindahkan ke Sampah Transaksi.');
        }

        if (empty(trim($reason))) {
            throw new InvalidArgumentException('Alasan pembatalan wajib diisi.');
        }

        $location = $sale->location;

        if (! $location) {
            throw new InvalidArgumentException('Lokasi aktif belum tersedia.');
        }

        return DB::transaction(function () use ($sale, $user, $reason, $location) {
            // 1. Revert Physical Inventory Stock
            foreach ($sale->items as $item) {
                if ($item->product_type_snapshot === 'PHYSICAL' && $item->product) {
                    $this->inventoryService->adjustStock(
                        product: $item->product,
                        location: $location,
                        quantityChange: $item->quantity, // Add back stock
                        movementType: 'TRASH_RESTORE',
                        notes: 'Pembatalan Transaksi #'.$sale->invoice_number.' - '.$reason,
                        user: $user,
                        reference: $sale
                    );
                }
            }

            // 2. Revert Payments & Balance Accounts
            foreach ($sale->payments as $payment) {
                if ($payment->balance_account_id) {
                    $account = BalanceAccount::whereKey($payment->balance_account_id)->lockForUpdate()->first();
                    if ($account) {
                        $before = $account->current_balance;
                        $after = $before - $payment->amount; // Deduct payment back

                        $account->update(['current_balance' => $after]);

                        BalanceTransaction::create([
                            'transaction_number' => 'TRX-'.Str::uuid(),
                            'transaction_type' => 'TRASH_REVERSAL',
                            'source_account_id' => $account->id,
                            'amount' => $payment->amount,
                            'balance_before' => $before,
                            'balance_after' => $after,
                            'reference_type' => Sale::class,
                            'reference_id' => $sale->id,
                            'description' => "Pembalian saldo akibat pembatalan POS #{$sale->invoice_number}",
                            'created_by' => $user->id,
                            'transaction_date' => now(),
                        ]);
                    }
                }
            }

            // 3. Revert Change Amount back to CASH account if change was given
            if ($sale->change_amount > 0) {
                $cashAccount = BalanceAccount::where('code', 'CASH')->where('status', 'ACTIVE')->lockForUpdate()->first();
                if ($cashAccount) {
                    $beforeCash = $cashAccount->current_balance;
                    $afterCash = $beforeCash + $sale->change_amount; // Put cash back

                    $cashAccount->update(['current_balance' => $afterCash]);

                    BalanceTransaction::create([
                        'transaction_number' => 'TRX-'.Str::uuid(),
                        'transaction_type' => 'TRASH_REVERSAL',
                        'destination_account_id' => $cashAccount->id,
                        'amount' => $sale->change_amount,
                        'balance_before' => $beforeCash,
                        'balance_after' => $afterCash,
                        'reference_type' => Sale::class,
                        'reference_id' => $sale->id,
                        'description' => "Pengembalian kembalian kasir akibat pembatalan POS #{$sale->invoice_number}",
                        'created_by' => $user->id,
                        'transaction_date' => now(),
                    ]);
                }
            }

            // 4. Update Sale Status to TRASHED
            $sale->update([
                'status' => 'TRASHED',
                'trash_reason' => $reason,
                'trashed_by' => $user->id,
                'trashed_at' => now(),
            ]);

            return true;
        });
    }

    /**
     * Restore a TRASHED sale back to COMPLETED status (if within 30-day retention).
     * Re-validates stock and re-applies stock deduct & balance transactions.
     */
    public function restoreFromTrash(Sale $sale, User $user): bool
    {
        if ($sale->status !== 'TRASHED') {
            throw new InvalidArgumentException('Hanya transaksi berstatus TRASHED yang dapat dipulihkan.');
        }

        if ($sale->trashed_at && $sale->trashed_at->diffInDays(now()) > 30) {
            throw new InvalidArgumentException('Transaksi yang dibatalkan lebih dari 30 hari telah melewati masa retensi dan tidak dapat di-restore.');
        }

        $location = $sale->location;

        if (! $location) {
            throw new InvalidArgumentException('Lokasi aktif belum tersedia.');
        }

        // Validate stock sufficiency for physical items before restoring
        foreach ($sale->items as $item) {
            if ($item->product_type_snapshot === 'PHYSICAL' && $item->product) {
                $availableStock = Inventory::where('product_id', $item->product_id)
                    ->where('location_id', $location->id)
                    ->value('quantity') ?? 0;

                if ($availableStock < $item->quantity) {
                    throw new InvalidArgumentException("Gagal restore: Stok produk '{$item->product_name_snapshot}' tidak mencukupi (Tersedia: {$availableStock}, Dibutuhkan: {$item->quantity}).");
                }
            }
        }

        return DB::transaction(function () use ($sale, $user, $location) {
            // 1. Re-deduct Physical Inventory
            foreach ($sale->items as $item) {
                if ($item->product_type_snapshot === 'PHYSICAL' && $item->product) {
                    $this->inventoryService->adjustStock(
                        product: $item->product,
                        location: $location,
                        quantityChange: -$item->quantity,
                        movementType: 'SALE',
                        notes: 'Restore Transaksi POS #'.$sale->invoice_number,
                        user: $user,
                        reference: $sale
                    );
                }
            }

            // 2. Re-apply Payments & Balance Accounts
            foreach ($sale->payments as $payment) {
                if ($payment->balance_account_id) {
                    $account = BalanceAccount::whereKey($payment->balance_account_id)->lockForUpdate()->first();
                    if ($account) {
                        $before = $account->current_balance;
                        $after = $before + $payment->amount;

                        $account->update(['current_balance' => $after]);

                        BalanceTransaction::create([
                            'transaction_number' => 'TRX-'.Str::uuid(),
                            'transaction_type' => 'RESTORE_REVERSAL',
                            'destination_account_id' => $account->id,
                            'amount' => $payment->amount,
                            'balance_before' => $before,
                            'balance_after' => $after,
                            'reference_type' => Sale::class,
                            'reference_id' => $sale->id,
                            'description' => "Pemulihan saldo akibat restore POS #{$sale->invoice_number}",
                            'created_by' => $user->id,
                            'transaction_date' => now(),
                        ]);
                    }
                }
            }

            // 3. Re-deduct Change Amount from CASH account
            if ($sale->change_amount > 0) {
                $cashAccount = BalanceAccount::where('code', 'CASH')->where('status', 'ACTIVE')->lockForUpdate()->first();
                if ($cashAccount) {
                    $beforeCash = $cashAccount->current_balance;
                    $afterCash = $beforeCash - $sale->change_amount;

                    $cashAccount->update(['current_balance' => $afterCash]);

                    BalanceTransaction::create([
                        'transaction_number' => 'TRX-'.Str::uuid(),
                        'transaction_type' => 'RESTORE_REVERSAL',
                        'source_account_id' => $cashAccount->id,
                        'amount' => $sale->change_amount,
                        'balance_before' => $beforeCash,
                        'balance_after' => $afterCash,
                        'reference_type' => Sale::class,
                        'reference_id' => $sale->id,
                        'description' => "Pengeluaran kembalian kasir akibat restore POS #{$sale->invoice_number}",
                        'created_by' => $user->id,
                        'transaction_date' => now(),
                    ]);
                }
            }

            // 4. Update Sale Status back to COMPLETED
            $sale->update([
                'status' => 'COMPLETED',
                'restored_by' => $user->id,
                'restored_at' => now(),
            ]);

            return true;
        });
    }

    /**
     * Auto retention job: Mark TRASHED sales older than 30 days as DELETED status.
     * Soft retention: Records remain in DB, but hidden from operational UI.
     */
    public function apply30DayAutoDeleteRetention(): int
    {
        $expiredSales = Sale::where('status', 'TRASHED')
            ->where('trashed_at', '<=', now()->subDays(30))
            ->get();

        $count = 0;
        foreach ($expiredSales as $sale) {
            $sale->update(['status' => 'DELETED']);
            $count++;
        }

        return $count;
    }
}
