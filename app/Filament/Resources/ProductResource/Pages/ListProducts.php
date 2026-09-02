<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Services\ProductImportService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import_excel')
                ->label('Import Excel / CSV')
                ->icon('heroicon-o-document-arrow-up')
                ->color('success')
                ->form([
                    Forms\Components\FileUpload::make('attachment')
                        ->label('Pilih File (.csv / .xlsx)')
                        ->acceptedFileTypes([
                            'text/csv',
                            'text/plain',
                            'application/csv',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])
                        ->required()
                        ->storeFiles(true)
                        ->disk('local')
                        ->directory('imports'),
                ])
                ->modalHeading('Import Master Produk dari Excel/CSV')
                ->modalDescription('Unggah file CSV dengan header: Kode, Nama, Kategori, Brand, Tipe, Harga Modal, Harga Jual, Barcode, Stok Awal, Stok Minimum, Provider Akun.')
                ->action(function (array $data): void {
                    $relativePath = $data['attachment'];
                    $fullPath = Storage::disk('local')->path($relativePath);

                    $result = app(ProductImportService::class)->importFromCsv($fullPath, auth()->user());

                    $title = "Import Selesai: {$result['imported_count']} produk baru, {$result['updated_count']} di-update.";
                    if ($result['incomplete_count'] > 0) {
                        $title .= " ({$result['incomplete_count']} berstatus HARGA INCOMPLETE)";
                    }

                    $notification = Notification::make()
                        ->title($title)
                        ->success();

                    if (!empty($result['errors'])) {
                        $notification->body('Beberapa baris dilewati karena error: ' . implode(' | ', array_slice($result['errors'], 0, 3)));
                    }

                    $notification->send();
                }),

            Actions\Action::make('download_template')
                ->label('Download Template CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('secondary')
                ->url(route('products.template-csv'))
                ->openUrlInNewTab(),

            Actions\CreateAction::make(),
        ];
    }
}
