<?php

use App\Livewire\Pos\Checkout;
use App\Models\Sale;
use App\Services\ProductImportService;
use Illuminate\Support\Facades\Route;

Route::redirect('/login', '/admin/login')->name('login');
Route::redirect('/', '/pos');

Route::middleware(['auth'])->group(function () {
    Route::get('/pos', Checkout::class)->name('pos');

    Route::get('/receipt/thermal/{sale}', function (Sale $sale) {
        $sale->load(['cashier', 'items', 'payments.paymentMethod']);
        return view('receipt.thermal', [
            'sale' => $sale,
            'paperWidth' => '58mm',
        ]);
    })->name('receipt.thermal');

    Route::get('/admin/products/template-csv', function () {
        $csvContent = app(ProductImportService::class)->generateCsvTemplate();
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="template_import_produk_raja_pos.csv"');
    })->name('products.template-csv');
});
