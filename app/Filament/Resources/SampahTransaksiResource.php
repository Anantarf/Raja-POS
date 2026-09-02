<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SampahTransaksiResource\Pages;
use App\Models\Sale;
use App\Services\SaleCancellationService;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SampahTransaksiResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-trash';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Sampah Transaksi';

    public static function table(Table $table): Table
    {
        $canRestore = auth()->user()?->hasPermission('sales.restore') ?? false;

        return $table
            ->query(fn () => Sale::where('status', 'TRASHED'))
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('No Invoice')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Tgl Transaksi')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('trash_reason')
                    ->label('Alasan Pembatalan')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('trashed_by_user.name')
                    ->label('Dibatalkan Oleh')
                    ->state(fn (Sale $record) => $record->trashed_by ? \App\Models\User::find($record->trashed_by)?->name : '-'),
                Tables\Columns\TextColumn::make('trashed_at')
                    ->label('Waktu Pembatalan')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Belanja')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'TRASHED' => 'warning',
                        'DELETED' => 'secondary',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('trashed_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('view_detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn (Sale $record) => 'Detail Transaksi Dibatalkan #' . $record->invoice_number)
                    ->modalContent(fn (Sale $record) => view('filament.pages.sale-detail-modal', ['sale' => $record])),
                Tables\Actions\Action::make('restore_sale')
                    ->label('Restore Transaksi')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Pulihkan Transaksi dari Sampah?')
                    ->modalDescription('Memulihkan transaksi akan memotong stok fisik kembali dan mencatat ulang mutasi saldo.')
                    ->action(function (Sale $record): void {
                        app(SaleCancellationService::class)->restoreFromTrash($record, auth()->user());

                        Notification::make()
                            ->title('Transaksi berhasil dipulihkan (Restore)')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Sale $record) => $canRestore && $record->status === 'TRASHED'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSampahTransaksi::route('/'),
        ];
    }
}
