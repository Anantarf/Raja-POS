<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BalanceAccountResource\Pages;
use App\Models\BalanceAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BalanceAccountResource extends Resource
{
    protected static ?string $model = BalanceAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 4;

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
                    ->unique(BalanceAccount::class, 'code', ignoreRecord: true),
                Forms\Components\Select::make('account_type')
                    ->options([
                        'CASH' => 'Kas Tunai',
                        'QRIS' => 'QRIS',
                        'BANK' => 'Bank',
                        'E_WALLET' => 'E-Wallet',
                        'PROVIDER' => 'Provider / Akun Modal',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('current_balance')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
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
                Tables\Columns\BadgeColumn::make('account_type')
                    ->colors([
                        'primary' => 'CASH',
                        'success' => 'QRIS',
                        'info' => 'BANK',
                        'warning' => 'E_WALLET',
                        'secondary' => 'PROVIDER',
                    ]),
                Tables\Columns\TextColumn::make('current_balance')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'ACTIVE',
                        'secondary' => 'INACTIVE',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('account_type')
                    ->options([
                        'CASH' => 'Kas Tunai',
                        'QRIS' => 'QRIS',
                        'BANK' => 'Bank',
                        'E_WALLET' => 'E-Wallet',
                        'PROVIDER' => 'Provider',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBalanceAccounts::route('/'),
        ];
    }
}
