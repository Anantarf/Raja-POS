<?php

namespace App\Filament\Resources\BalanceAccountResource\Pages;

use App\Filament\Resources\BalanceAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBalanceAccounts extends ManageRecords
{
    protected static string $resource = BalanceAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
