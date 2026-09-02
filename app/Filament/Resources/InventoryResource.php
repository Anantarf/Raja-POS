<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Product;
use App\Services\InventoryService;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Stok & Inventaris';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Stok Fisik';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('product.image_path')
                    ->label('Gambar')
                    ->disk('public')
                    ->defaultImageUrl(fn (Inventory $record) => $record->product->image_url)
                    ->square(),
                Tables\Columns\TextColumn::make('product.code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('location.name')
                    ->label('Lokasi')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Stok Saat Ini')
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.minimum_stock')
                    ->label('Stok Min.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_status')
                    ->label('Status Stok')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'AVAILABLE' => 'success',
                        'LOW_STOCK' => 'warning',
                        'OUT_OF_STOCK' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'AVAILABLE' => 'TERSEDIA',
                        'LOW_STOCK' => 'MENIPIS',
                        'OUT_OF_STOCK' => 'HABIS',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('last_stock_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('location_id')
                    ->label('Lokasi')
                    ->relationship('location', 'name'),
                Tables\Filters\Filter::make('low_stock')
                    ->label('Stok Menipis / Habis')
                    ->query(fn ($query) => $query->whereHas('product', fn ($q) => $q->whereRaw('inventories.quantity <= products.minimum_stock'))),
            ])
            ->actions([
                Tables\Actions\Action::make('adjust_stock')
                    ->label('Penyesuaian Stok')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('movement_type')
                            ->label('Jenis Penyesuaian')
                            ->options([
                                'ADJUSTMENT_IN' => 'Stok Masuk (Adjustment In)',
                                'ADJUSTMENT_OUT' => 'Stok Keluar (Adjustment Out)',
                                'DAMAGE' => 'Barang Rusak / Damage',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Jumlah (PCS)')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Alasan / Catatan')
                            ->required(),
                    ])
                    ->action(function (Inventory $record, array $data): void {
                        $qtyChange = match ($data['movement_type']) {
                            'ADJUSTMENT_IN' => (int) $data['quantity'],
                            'ADJUSTMENT_OUT', 'DAMAGE' => -((int) $data['quantity']),
                            default => (int) $data['quantity'],
                        };

                        app(InventoryService::class)->adjustStock(
                            product: $record->product,
                            location: $record->location,
                            quantityChange: $qtyChange,
                            movementType: $data['movement_type'],
                            notes: $data['notes'],
                            user: auth()->user()
                        );

                        Notification::make()
                            ->title('Stok berhasil diperbarui')
                            ->success()
                            ->send();
                    })
                    ->visible(fn () => auth()->user()?->hasPermission('inventory.adjust') ?? false),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageInventories::route('/'),
        ];
    }
}
