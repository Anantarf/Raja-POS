<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryMovementResource\Pages;
use App\Models\InventoryMovement;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InventoryMovementResource extends Resource
{
    protected static ?string $model = InventoryMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Riwayat Pergerakan Stok';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal & Waktu')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.code')
                    ->label('Kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\BadgeColumn::make('movement_type')
                    ->label('Tipe Pergerakan')
                    ->colors([
                        'primary' => 'SALE',
                        'success' => 'ADJUSTMENT_IN',
                        'danger' => 'ADJUSTMENT_OUT',
                        'warning' => 'DAMAGE',
                        'info' => 'STOCK_OPNAME',
                        'secondary' => 'TRASH_RESTORE',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'SALE' => 'Penjualan (SALE)',
                        'ADJUSTMENT_IN' => 'Stok Masuk',
                        'ADJUSTMENT_OUT' => 'Stok Keluar',
                        'DAMAGE' => 'Rusak / Damage',
                        'STOCK_OPNAME' => 'Stok Opname',
                        'TRASH_RESTORE' => 'Batal Transaksi / Restore',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('quantity_before')
                    ->label('Sebelum'),
                Tables\Columns\TextColumn::make('quantity_change')
                    ->label('Perubahan')
                    ->formatStateUsing(fn (int $state): string => ($state > 0 ? '+' : '') . $state)
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('quantity_after')
                    ->label('Sesudah'),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(40),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Oleh')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('movement_type')
                    ->label('Tipe Pergerakan')
                    ->options([
                        'SALE' => 'Penjualan',
                        'ADJUSTMENT_IN' => 'Stok Masuk',
                        'ADJUSTMENT_OUT' => 'Stok Keluar',
                        'DAMAGE' => 'Rusak / Damage',
                        'STOCK_OPNAME' => 'Stok Opname',
                        'TRASH_RESTORE' => 'Restore Pembatalan',
                    ]),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageInventoryMovements::route('/'),
        ];
    }
}
