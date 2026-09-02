<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BalanceTransactionResource\Pages;
use App\Models\BalanceAccount;
use App\Models\BalanceTransaction;
use App\Services\BalanceService;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BalanceTransactionResource extends Resource
{
    protected static ?string $model = BalanceTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Keuangan & Rekening';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Audit Mutasi Saldo';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_number')
                    ->label('No. Mutasi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Tanggal & Waktu')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_type')
                    ->label('Tipe Mutasi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SALE_RECEIPT', 'DEPOSIT' => 'success',
                        'WITHDRAWAL' => 'danger',
                        'TRANSFER' => 'info',
                        'TRASH_REVERSAL' => 'warning',
                        'RESTORE_REVERSAL' => 'primary',
                        'ADJUSTMENT' => 'secondary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('sourceAccount.name')
                    ->label('Akun Asal')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('destinationAccount.name')
                    ->label('Akun Tujuan')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance_before')
                    ->label('Saldo Sebelum')
                    ->money('IDR')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('balance_after')
                    ->label('Saldo Sesudah')
                    ->money('IDR')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->placeholder('Sistem'),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('transaction_type')
                    ->label('Tipe Mutasi')
                    ->options([
                        'SALE_RECEIPT' => 'Penerimaan Penjualan',
                        'DEPOSIT' => 'Deposit / Uang Masuk',
                        'WITHDRAWAL' => 'Penarikan Uang',
                        'TRANSFER' => 'Transfer Antar Akun',
                        'TRASH_REVERSAL' => 'Pembalian Sampah Transaksi',
                        'RESTORE_REVERSAL' => 'Restore Transaksi',
                        'ADJUSTMENT' => 'Penyesuaian Saldo',
                    ]),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBalanceTransactions::route('/'),
        ];
    }
}
