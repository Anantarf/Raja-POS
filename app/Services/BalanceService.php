<?php

namespace App\Services;

use App\Models\BalanceAccount;
use App\Models\BalanceTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class BalanceService
{
    public function transfer(BalanceAccount $fromAccount, BalanceAccount $toAccount, float $amount, string $description, User $user): BalanceTransaction
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Nominal transfer harus lebih dari 0.');
        }

        if ($fromAccount->id === $toAccount->id) {
            throw new InvalidArgumentException('Akun asal dan akun tujuan transfer tidak boleh sama.');
        }

        return DB::transaction(function () use ($fromAccount, $toAccount, $amount, $description, $user) {
            $accounts = BalanceAccount::whereIn('id', [$fromAccount->id, $toAccount->id])
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $from = $accounts->get($fromAccount->id);
            $to = $accounts->get($toAccount->id);

            if (! $from || ! $to || $from->status !== 'ACTIVE' || $to->status !== 'ACTIVE') {
                throw new InvalidArgumentException('Akun saldo tidak aktif atau tidak ditemukan.');
            }
            if ((float) $from->current_balance < $amount) {
                throw new InvalidArgumentException('Saldo akun asal tidak mencukupi untuk transfer.');
            }

            $fromBefore = (float) $from->current_balance;
            $toBefore = (float) $to->current_balance;
            $from->update(['current_balance' => $fromBefore - $amount]);
            $to->update(['current_balance' => $toBefore + $amount]);

            return BalanceTransaction::create([
                'transaction_number' => 'TRF-'.Str::uuid(),
                'transaction_type' => 'TRANSFER',
                'source_account_id' => $from->id,
                'destination_account_id' => $to->id,
                'amount' => $amount,
                'balance_before' => $fromBefore,
                'balance_after' => $fromBefore - $amount,
                'destination_balance_before' => $toBefore,
                'destination_balance_after' => $toBefore + $amount,
                'description' => $description,
                'created_by' => $user->id,
                'transaction_date' => now(),
            ]);
        });
    }

    public function deposit(BalanceAccount $account, float $amount, string $description, User $user): BalanceTransaction
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Nominal setoran harus lebih dari 0.');
        }

        return $this->updateSingleAccount($account, $amount, 'DEPOSIT', 'destination_account_id', $description, $user);
    }

    public function withdraw(BalanceAccount $account, float $amount, string $description, User $user): BalanceTransaction
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Nominal penarikan harus lebih dari 0.');
        }

        return $this->updateSingleAccount($account, -$amount, 'WITHDRAWAL', 'source_account_id', $description, $user);
    }

    public function adjustBalance(BalanceAccount $account, float $newBalance, string $reason, User $user): BalanceTransaction
    {
        if ($newBalance < 0) {
            throw new InvalidArgumentException('Saldo hasil penyesuaian tidak boleh negatif.');
        }

        return DB::transaction(function () use ($account, $newBalance, $reason, $user) {
            $locked = BalanceAccount::whereKey($account->id)->lockForUpdate()->firstOrFail();
            if ($locked->status !== 'ACTIVE') {
                throw new InvalidArgumentException('Akun saldo tidak aktif.');
            }

            $before = (float) $locked->current_balance;
            $locked->update(['current_balance' => $newBalance]);

            return BalanceTransaction::create([
                'transaction_number' => 'ADJ-'.Str::uuid(),
                'transaction_type' => 'ADJUSTMENT',
                'destination_account_id' => $locked->id,
                'amount' => abs($newBalance - $before),
                'balance_before' => $before,
                'balance_after' => $newBalance,
                'description' => 'Penyesuaian Saldo: '.$reason,
                'created_by' => $user->id,
                'transaction_date' => now(),
            ]);
        });
    }

    private function updateSingleAccount(BalanceAccount $account, float $change, string $type, string $accountColumn, string $description, User $user): BalanceTransaction
    {
        return DB::transaction(function () use ($account, $change, $type, $accountColumn, $description, $user) {
            $locked = BalanceAccount::whereKey($account->id)->lockForUpdate()->firstOrFail();
            if ($locked->status !== 'ACTIVE') {
                throw new InvalidArgumentException('Akun saldo tidak aktif.');
            }

            $before = (float) $locked->current_balance;
            $after = $before + $change;
            if ($after < 0) {
                throw new InvalidArgumentException('Saldo akun tidak mencukupi.');
            }
            $locked->update(['current_balance' => $after]);

            return BalanceTransaction::create([
                'transaction_number' => strtolower(substr($type, 0, 3)).'-'.Str::uuid(),
                'transaction_type' => $type,
                $accountColumn => $locked->id,
                'amount' => abs($change),
                'balance_before' => $before,
                'balance_after' => $after,
                'description' => $description,
                'created_by' => $user->id,
                'transaction_date' => now(),
            ]);
        });
    }
}
