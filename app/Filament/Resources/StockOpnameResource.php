<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockOpnameResource\Pages;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\StockOpname;
use App\Services\InventoryService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StockOpnameResource extends Resource
{
    protected static ?string $model = StockOpname::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Sesi Stock Opname';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Sesi Opname')
                    ->schema([
                        Forms\Components\TextInput::make('opname_number')
                            ->label('Nomor Opname')
                            ->default(fn () => 'SOP-' . date('Ymd') . '-' . sprintf('%04d', rand(100, 999)))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('location_id')
                            ->label('Lokasi Toko')
                            ->relationship('location', 'name')
                            ->required()
                            ->default(fn () => \App\Models\Location::where('code', 'RAJA-BANGO')->first()?->id),
                        Forms\Components\Select::make('status')
                            ->label('Status Sesi')
                            ->options([
                                'DRAFT' => 'Draft',
                                'COMPLETED' => 'Disetujui / Completed',
                                'CANCELLED' => 'Dibatalkan',
                            ])
                            ->default('DRAFT')
                            ->disabled()
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Daftar Produk Opname')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Produk Fisik')
                                    ->options(fn () => Product::where('product_type', 'PHYSICAL')->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        $locationId = $get('../../location_id') ?? \App\Models\Location::where('code', 'RAJA-BANGO')->first()?->id;
                                        if ($state && $locationId) {
                                            $sysQty = Inventory::where('product_id', $state)->where('location_id', $locationId)->value('quantity') ?? 0;
                                            $set('system_quantity', $sysQty);
                                            $physQty = (int) ($get('physical_quantity') ?? 0);
                                            $set('difference', $physQty - $sysQty);
                                        }
                                    }),
                                Forms\Components\TextInput::make('system_quantity')
                                    ->label('Stok Sistem')
                                    ->numeric()
                                    ->default(0)
                                    ->readOnly()
                                    ->required(),
                                Forms\Components\TextInput::make('physical_quantity')
                                    ->label('Stok Fisik')
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        $sysQty = (int) ($get('system_quantity') ?? 0);
                                        $physQty = (int) ($state ?? 0);
                                        $set('difference', $physQty - $sysQty);
                                    }),
                                Forms\Components\TextInput::make('difference')
                                    ->label('Selisih')
                                    ->numeric()
                                    ->default(0)
                                    ->readOnly(),
                                Forms\Components\TextInput::make('notes')
                                    ->label('Catatan')
                                    ->placeholder('Alasan selisih'),
                            ])
                            ->columns(5)
                            ->defaultItems(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('opname_number')
                    ->label('Nomor Sesi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location.name')
                    ->label('Lokasi')
                    ->sortable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Jumlah Item'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'DRAFT' => 'warning',
                        'COMPLETED' => 'success',
                        'CANCELLED' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Dibuat Oleh'),
                Tables\Columns\TextColumn::make('approver.name')
                    ->label('Disetujui Oleh'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'DRAFT' => 'Draft',
                        'COMPLETED' => 'Disetujui',
                        'CANCELLED' => 'Dibatalkan',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Setujui Opname')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Approval Stock Opname')
                    ->modalDescription('Menyetujui sesi ini akan memperbarui stok fisik aktual dan mencatat pergerakan stok opname.')
                    ->action(function (StockOpname $record): void {
                        app(InventoryService::class)->approveStockOpname($record, auth()->user());

                        Notification::make()
                            ->title('Stock Opname berhasil disetujui & stok diperbarui')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (StockOpname $record) => $record->status === 'DRAFT' && (auth()->user()?->hasPermission('stock_opname.approve') ?? false)),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockOpnames::route('/'),
            'create' => Pages\CreateStockOpname::route('/create'),
            'edit' => Pages\EditStockOpname::route('/{record}/edit'),
        ];
    }
}
