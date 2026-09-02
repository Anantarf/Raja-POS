<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesResource\Pages;
use App\Models\Sale;
use App\Services\SaleCancellationService;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SalesResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Riwayat Penjualan';

    public static function table(Table $table): Table
    {
        $canViewProfit = auth()->user()?->hasPermission('report.profit.view') ?? false;
        $canTrash = auth()->user()?->hasPermission('sales.trash') ?? false;

        return $table
            ->query(fn () => Sale::where('status', 'COMPLETED'))
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('No Invoice')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Tanggal & Waktu')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cashier.name')
                    ->label('Kasir')
                    ->sortable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Item'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Belanja')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_cost')
                    ->label('Total Modal')
                    ->money('IDR')
                    ->sortable()
                    ->visible($canViewProfit),
                Tables\Columns\TextColumn::make('gross_profit')
                    ->label('Profit Gross')
                    ->money('IDR')
                    ->sortable()
                    ->visible($canViewProfit),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'COMPLETED' => 'success',
                        'TRASHED' => 'warning',
                        'DELETED' => 'secondary',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->filters([
                Tables\Filters\Filter::make('today')
                    ->label('Hari Ini')
                    ->query(fn ($query) => $query->whereDate('transaction_date', today())),
            ])
            ->actions([
                Tables\Actions\Action::make('view_detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn (Sale $record) => 'Detail Transaksi #' . $record->invoice_number)
                    ->modalContent(fn (Sale $record) => view('filament.pages.sale-detail-modal', ['sale' => $record])),
                Tables\Actions\Action::make('print_receipt')
                    ->label('Struk')
                    ->icon('heroicon-o-printer')
                    ->color('secondary')
                    ->url(fn (Sale $record) => route('receipt.thermal', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('move_to_trash')
                    ->label('Ke Sampah Transaksi')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Alasan Pembatalan (Wajib)')
                            ->required()
                            ->rows(3),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan & Pindahkan ke Sampah Transaksi?')
                    ->modalDescription('Tindakan ini akan mengembalikan stok fisik dan membalik mutasi saldo secara otomatis.')
                    ->action(function (Sale $record, array $data): void {
                        app(SaleCancellationService::class)->moveToTrash($record, auth()->user(), $data['reason']);

                        Notification::make()
                            ->title('Transaksi berhasil dipindahkan ke Sampah Transaksi')
                            ->success()
                            ->send();
                    })
                    ->visible(fn () => $canTrash),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSales::route('/'),
        ];
    }
}
