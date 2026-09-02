<?php

use App\Livewire\Pos\Checkout;
use App\Models\Sale;
use Illuminate\Support\Facades\Route;

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
});
