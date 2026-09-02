<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $canViewCostPrice = auth()->user()?->hasPermission('cost_price.view') ?? false;

        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Produk')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Produk / SKU')
                            ->required()
                            ->maxLength(255)
                            ->unique(Product::class, 'code', ignoreRecord: true),
                        Forms\Components\TextInput::make('barcode')
                            ->label('Barcode (Opsional)')
                            ->helperText('Jika kosong, kode produk digunakan sebagai barcode internal')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Produk')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('brand_id')
                            ->label('Merk / Brand')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('product_type')
                            ->label('Tipe Produk')
                            ->options([
                                'PHYSICAL' => 'Fisik',
                                'DIGITAL' => 'Digital',
                                'SERVICE' => 'Service / Layanan',
                            ])
                            ->default('PHYSICAL')
                            ->required(),
                        Forms\Components\TextInput::make('product_subtype')
                            ->label('Subtipe Produk')
                            ->placeholder('Contoh: Pulsa, Paket Data, Tarik Tunai'),
                        Forms\Components\Select::make('default_balance_account_id')
                            ->label('Akun Saldo Modal Utama')
                            ->relationship('defaultBalanceAccount', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Opsional. Digunakan untuk produk digital (MULTI) atau layanan tertentu'),
                    ])->columns(2),

                Forms\Components\Section::make('Harga & Stok')
                    ->schema([
                        Forms\Components\TextInput::make('cost_price')
                            ->label('Harga Modal')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->visible($canViewCostPrice),
                        Forms\Components\TextInput::make('selling_price')
                            ->label('Harga Jual')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->required(),
                        Forms\Components\TextInput::make('minimum_stock')
                            ->label('Stok Minimum')
                            ->numeric()
                            ->default(3)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status Produk')
                            ->options([
                                'ACTIVE' => 'Aktif',
                                'INACTIVE' => 'Inaktif',
                                'DISCONTINUED' => 'Dihentikan',
                            ])
                            ->default('ACTIVE')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Gambar & Deskripsi')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Gambar Produk')
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->maxSize(2048),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        $canViewCostPrice = auth()->user()?->hasPermission('cost_price.view') ?? false;

        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Gambar')
                    ->disk('public')
                    ->defaultImageUrl(fn (Product $record) => $record->image_url)
                    ->square(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('product_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PHYSICAL' => 'primary',
                        'DIGITAL' => 'success',
                        'SERVICE' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'PHYSICAL' => 'Fisik',
                        'DIGITAL' => 'Digital',
                        'SERVICE' => 'Service',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_price')
                    ->label('Harga Modal')
                    ->money('IDR')
                    ->sortable()
                    ->visible($canViewCostPrice),
                Tables\Columns\TextColumn::make('selling_price')
                    ->label('Harga Jual')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_status')
                    ->label('Status Harga')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'COMPLETE' => 'success',
                        'INCOMPLETE' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'COMPLETE' => 'Lengkap',
                        'INCOMPLETE' => 'Harga 0 (Incomplete)',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ACTIVE' => 'success',
                        'INACTIVE' => 'secondary',
                        'DISCONTINUED' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ACTIVE' => 'Aktif',
                        'INACTIVE' => 'Inaktif',
                        'DISCONTINUED' => 'Dihentikan',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_type')
                    ->label('Tipe Produk')
                    ->options([
                        'PHYSICAL' => 'Fisik',
                        'DIGITAL' => 'Digital',
                        'SERVICE' => 'Service',
                    ]),
                Tables\Filters\SelectFilter::make('price_status')
                    ->label('Status Harga')
                    ->options([
                        'COMPLETE' => 'Lengkap',
                        'INCOMPLETE' => 'Incomplete',
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('brand')
                    ->relationship('brand', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
