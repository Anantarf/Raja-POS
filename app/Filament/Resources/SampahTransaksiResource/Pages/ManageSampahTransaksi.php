<?php

namespace App\Filament\Resources\SampahTransaksiResource\Pages;

use App\Filament\Resources\SampahTransaksiResource;
use Filament\Resources\Pages\ManageRecords;

class ManageSampahTransaksi extends ManageRecords
{
    protected static string $resource = SampahTransaksiResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
