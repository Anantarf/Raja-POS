<?php

namespace App\Filament\Resources\BalanceTransactionResource\Pages;

use App\Filament\Resources\BalanceTransactionResource;
use App\Models\BalanceAccount;
use App\Services\BalanceService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageBalanceTransactions extends ManageRecords
{
    protected static string $resource = BalanceTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('transfer')
                ->label('Transfer Antar Akun')
                ->icon('heroicon-o-arrows-right-left')
                ->color('info')
                ->form([
                    Forms\Components\Select::make('from_account_id')
                        ->label('Akun Asal')
                        ->options(BalanceAccount::where('status', 'ACTIVE')->pluck('name', 'id'))
                        ->required(),
                    Forms\Components\Select::make('to_account_id')
                        ->label('Akun Tujuan')
                        ->options(BalanceAccount::where('status', 'ACTIVE')->pluck('name', 'id'))
                        ->required(),
                    Forms\Components\TextInput::make('amount')
                        ->label('Nominal Transfer')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),
                    Forms\Components\Textarea::make('description')
                        ->label('Keterangan')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $from = BalanceAccount::findOrFail($data['from_account_id']);
                    $to = BalanceAccount::findOrFail($data['to_account_id']);

                    app(BalanceService::class)->transfer($from, $to, (float) $data['amount'], $data['description'], auth()->user());

                    Notification::make()
                        ->title('Transfer saldo berhasil')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('deposit')
                ->label('Deposit / Uang Masuk')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    Forms\Components\Select::make('account_id')
                        ->label('Akun Saldo Tujuan')
                        ->options(BalanceAccount::where('status', 'ACTIVE')->pluck('name', 'id'))
                        ->required(),
                    Forms\Components\TextInput::make('amount')
                        ->label('Nominal Deposit')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),
                    Forms\Components\Textarea::make('description')
                        ->label('Keterangan')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $account = BalanceAccount::findOrFail($data['account_id']);
                    app(BalanceService::class)->deposit($account, (float) $data['amount'], $data['description'], auth()->user());

                    Notification::make()
                        ->title('Deposit saldo berhasil')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('withdraw')
                ->label('Penarikan Uang')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('danger')
                ->form([
                    Forms\Components\Select::make('account_id')
                        ->label('Akun Saldo Asal')
                        ->options(BalanceAccount::where('status', 'ACTIVE')->pluck('name', 'id'))
                        ->required(),
                    Forms\Components\TextInput::make('amount')
                        ->label('Nominal Penarikan')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),
                    Forms\Components\Textarea::make('description')
                        ->label('Keterangan')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $account = BalanceAccount::findOrFail($data['account_id']);
                    app(BalanceService::class)->withdraw($account, (float) $data['amount'], $data['description'], auth()->user());

                    Notification::make()
                        ->title('Penarikan saldo berhasil')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('adjust')
                ->label('Penyesuaian Saldo (Rekonsiliasi)')
                ->icon('heroicon-o-adjustments-horizontal')
                ->color('warning')
                ->form([
                    Forms\Components\Select::make('account_id')
                        ->label('Akun Saldo')
                        ->options(BalanceAccount::where('status', 'ACTIVE')->pluck('name', 'id'))
                        ->required(),
                    Forms\Components\TextInput::make('new_balance')
                        ->label('Saldo Baru (Riil)')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),
                    Forms\Components\Textarea::make('reason')
                        ->label('Alasan Penyesuaian')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $account = BalanceAccount::findOrFail($data['account_id']);
                    app(BalanceService::class)->adjustBalance($account, (float) $data['new_balance'], $data['reason'], auth()->user());

                    Notification::make()
                        ->title('Penyesuaian saldo berhasil')
                        ->success()
                        ->send();
                }),
        ];
    }
}
