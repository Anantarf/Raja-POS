<?php

use App\Livewire\Admin\Balances;
use App\Livewire\Admin\Brands;
use App\Livewire\Admin\Categories;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Inventories;
use App\Livewire\Admin\InventoryMovements;
use App\Livewire\Admin\Products;
use App\Livewire\Admin\Reports;
use App\Livewire\Admin\Sales;
use App\Livewire\Admin\SampahTransaksi;
use App\Livewire\Admin\Settings;
use App\Livewire\Admin\StockOpname;
use App\Livewire\Auth\Login;
use App\Livewire\Pos\Checkout;
use App\Models\Sale;
use App\Services\ProductImportService;
use Illuminate\Support\Facades\Route;

Route::get('/login', Login::class)->name('login');
Route::get('/admin/login', Login::class)->name('admin.login');
Route::redirect('/', '/pos');

Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    })->name('logout');

    // Livewire Kasir POS
    Route::get('/pos', Checkout::class)->name('pos');

    // Custom Livewire Admin Routes
    Route::get('/admin', Dashboard::class)->name('admin');
    Route::get('/admin/dashboard', Dashboard::class)->name('admin.dashboard');
    Route::get('/admin/sales', Sales::class)->name('admin.sales');
    Route::get('/admin/trash', SampahTransaksi::class)->name('admin.trash');
    Route::get('/admin/inventories', Inventories::class)->name('admin.inventories');
    Route::get('/admin/inventory-movements', InventoryMovements::class)->name('admin.inventory-movements');
    Route::get('/admin/stock-opname', StockOpname::class)->name('admin.stock-opname');
    Route::get('/admin/products', Products::class)->name('admin.products');
    Route::get('/admin/categories', Categories::class)->name('admin.categories');
    Route::get('/admin/brands', Brands::class)->name('admin.brands');
    Route::get('/admin/balances', Balances::class)->name('admin.balances');
    Route::get('/admin/reports/{type?}', Reports::class)->name('admin.reports');
    Route::get('/admin/settings/{section?}', Settings::class)->name('admin.settings');

    // Also support /portal/* route aliases
    Route::get('/portal/sales', Sales::class)->name('portal.sales');
    Route::get('/portal/trash', SampahTransaksi::class)->name('portal.trash');
    Route::get('/portal/inventories', Inventories::class)->name('portal.inventories');
    Route::get('/portal/inventory-movements', InventoryMovements::class)->name('portal.inventory-movements');
    Route::get('/portal/stock-opname', StockOpname::class)->name('portal.stock-opname');
    Route::get('/portal/products', Products::class)->name('portal.products');
    Route::get('/portal/categories', Categories::class)->name('portal.categories');
    Route::get('/portal/brands', Brands::class)->name('portal.brands');
    Route::get('/portal/balances', Balances::class)->name('portal.balances');
    Route::get('/portal/reports/{type?}', Reports::class)->name('portal.reports');
    Route::get('/portal/settings/{section?}', Settings::class)->name('portal.settings');

    // Receipt Route
    Route::get('/receipt/thermal/{sale}', function (Sale $sale) {
        $sale->load(['cashier', 'items', 'payments.paymentMethod']);

        return view('receipt.thermal', [
            'sale' => $sale,
            'paperWidth' => '58mm',
        ]);
    })->name('receipt.thermal');

    // Download CSV Template
    Route::get('/admin/products/template-csv', function () {
        $csvContent = app(ProductImportService::class)->generateCsvTemplate();

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="template_import_produk_raja_pos.csv"');
    })->name('products.template-csv');
});
