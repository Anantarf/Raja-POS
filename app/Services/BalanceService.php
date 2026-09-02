<?php

namespace App\Services;

use App\Models\BalanceAccount;
use App\Models\BalanceTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BalanceService
{
    /**
     * Transfer funds between two balance accounts atomically.
     */
    public function transfer(
        BalanceAccount $fromAccount,
        BalanceAccount $toAccount,
        float $amount,
        string $description,
        User $user
    ): BalanceTransaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Nominal transfer harus lebih dari 0.');
        }

        if ($fromAccount->id === $toAccount->id) {
            throw new InvalidArgumentException('Akun asal dan akun tujuan transfer tidak boleh sama.');
        }

        return DB::transaction(function () use ($fromAccount, $toAccount, $amount, $description, $user) {
            $fromBefore = $fromAccount->current_balance;
            $fromAfter = $fromBefore - $amount;
            $fromAccount->update(['current_balance' => $fromAfter]);

            $toBefore = $toAccount->current_balance;
            $toAfter = $toBefore + $amount;
            $toAccount->update(['current_balance' => $toAfter]);

            return BalanceTransaction::create([
                'transaction_number' => 'TRF-' . date('YmdHis') . '-' . sprintf('%03d', rand(100, 999)),
                'transaction_type' => 'TRANSFER',
                'source_account_id' => $fromAccount->id,
                'destination_account_id' => $toAccount->id,
                'amount' => $amount,
                'balance_before' => $fromBefore,
                'balance_after' => $fromAfter,
                'description' => $description,
                'created_by' => $user->id,
                'transaction_date' => now(),
            ]);
        });
    }

    /**
     * Deposit funds into a balance account.
     */
    public function deposit(
        BalanceAccount $account,
        float $amount,
        string $description,
        User $user
    ): BalanceTransaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Nominal deposit harus lebih dari 0.');
        }

        return DB::transaction(function () use ($account, $amount, $description, $user) {
            $before = $account->current_balance;
            $after = $before + $amount;
            $account->update(['current_balance' => $after]);

            return BalanceTransaction::create([
                'transaction_number' => 'DEP-' . date('YmdHis') . '-' . sprintf('%03d', rand(100, 999)),
                'transaction_type' => 'DEPOSIT',
                'destination_account_id' => $account->id,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'description' => $description,
                'created_by' => $user->id,
                'transaction_date' => now(),
            ]);
        });
    }

    /**
     * Withdraw funds from a balance account.
     */
    public function withdraw(
        BalanceAccount $account,
        float $amount,
        string $description,
        User $user
    ): BalanceTransaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Nominal penarikan harus lebih dari 0.');
        }

        return DB::transaction(function () use ($account, $amount, $description, $user) {
            $before = $account->current_balance;
            $after = $before - $amount;
            $account->update(['current_balance' => $after]);

            return BalanceTransaction::create([
                'transaction_number' => 'WDR-' . date('YmdHis') . '-' . sprintf('%03d', rand(100, 999)),
                'transaction_type' => 'WITHDRAWAL',
                'source_account_id' => $account->id,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'description' => $description,
                'created_by' => $user->id,
                'transaction_date' => now(),
            ]);
        });
    }

    /**
     * Manual adjustment to balance account to reconcile real-world bank/cash balance.
     */
    public function adjustBalance(
        BalanceAccount $account,
        float $newBalance,
        string $reason,
        User $user
    ): BalanceTransaction {
        return DB::transaction(function () use ($account, $newBalance, $reason, $user) {
            $before = $account->current_balance;
            $account->update(['current_balance' => $newBalance]);

            return BalanceTransaction::create([
                'transaction_number' => 'ADJ-' . date('YmdHis') . '-' . sprintf('%03d', rand(100, 999)),
                'transaction_type' => 'ADJUSTMENT',
                'destination_account_id' => $account->id,
                'amount' => abs($newBalance - $before),
                'balance_before' => $before,
                'balance_after' => $newBalance,
                'description' => 'Penyesuaian Saldo: ' . $reason,
                'created_by' => $user->id,
                'transaction_date' => now(),
            ]);
        });
    }
}
