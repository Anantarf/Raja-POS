<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentMethodResource\Pages;
use App\Models\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255)
                    ->unique(PaymentMethod::class, 'code', ignoreRecord: true),
                Forms\Components\Select::make('type')
                    ->options([
                        'CASH' => 'Cash / Tunai',
                        'QRIS' => 'QRIS',
                        'TRANSFER' => 'Transfer Bank',
                        'E_WALLET' => 'E-Wallet',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'ACTIVE' => 'Aktif',
                        'INACTIVE' => 'Inaktif',
                    ])
                    ->default('ACTIVE')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'CASH',
                        'success' => 'QRIS',
                        'info' => 'TRANSFER',
                        'warning' => 'E_WALLET',
                    ]),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'ACTIVE',
                        'secondary' => 'INACTIVE',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePaymentMethods::route('/'),
        ];
    }
}
