<?php

namespace App\Services;

use App\Jobs\ProcessAuditLogJob;
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

        $trx = DB::transaction(function () use ($fromAccount, $toAccount, $amount, $description, $user) {
            $accounts = BalanceAccount::forUserLocation($user)
                ->whereIn('id', [$fromAccount->id, $toAccount->id])
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $from = $accounts->get($fromAccount->id);
            $to = $accounts->get($toAccount->id);

            if (! $from || ! $to || $from->status !== 'ACTIVE' || $to->status !== 'ACTIVE') {
                throw new InvalidArgumentException('Akun saldo tidak aktif, tidak ditemukan, atau di luar lokasi cabang Anda.');
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

        ProcessAuditLogJob::dispatch(
            action: 'BALANCE_TRANSFER',
            description: "Transfer saldo Rp".number_format($amount, 0, ',', '.')." dari {$fromAccount->name} ke {$toAccount->name}",
            userId: $user->id,
            context: [
                'from_account_id' => $fromAccount->id,
                'to_account_id' => $toAccount->id,
                'amount' => $amount,
                'description' => $description,
            ]
        );

        return $trx;
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

        $trx = DB::transaction(function () use ($account, $newBalance, $reason, $user) {
            $locked = BalanceAccount::forUserLocation($user)->whereKey($account->id)->lockForUpdate()->first();
            if (! $locked || $locked->status !== 'ACTIVE') {
                throw new InvalidArgumentException('Akun saldo tidak aktif atau di luar lokasi cabang Anda.');
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

        ProcessAuditLogJob::dispatch(
            action: 'BALANCE_ADJUSTMENT',
            description: "Penyesuaian saldo akun {$account->name} menjadi Rp".number_format($newBalance, 0, ',', '.').". Alasan: {$reason}",
            userId: $user->id,
            context: [
                'account_id' => $account->id,
                'new_balance' => $newBalance,
                'reason' => $reason,
            ]
        );

        return $trx;
    }

    private function updateSingleAccount(BalanceAccount $account, float $change, string $type, string $accountColumn, string $description, User $user): BalanceTransaction
    {
        $trx = DB::transaction(function () use ($account, $change, $type, $accountColumn, $description, $user) {
            $locked = BalanceAccount::forUserLocation($user)->whereKey($account->id)->lockForUpdate()->first();
            if (! $locked || $locked->status !== 'ACTIVE') {
                throw new InvalidArgumentException('Akun saldo tidak aktif atau di luar lokasi cabang Anda.');
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

        ProcessAuditLogJob::dispatch(
            action: 'BALANCE_'.$type,
            description: "Transaksi {$type} sebesar Rp".number_format(abs($change), 0, ',', '.')." pada akun {$account->name}",
            userId: $user->id,
            context: [
                'account_id' => $account->id,
                'amount' => abs($change),
                'type' => $type,
                'description' => $description,
            ]
        );

        return $trx;
    }
}
