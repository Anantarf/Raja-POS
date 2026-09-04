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
    Route::get('/pos', Checkout::class)->middleware('can:sales.create')->name('pos');

    // Custom Livewire Operational Portal Routes (/portal/*)
    Route::get('/portal', Dashboard::class)->middleware('can:dashboard.view')->name('portal.dashboard');
    Route::get('/portal/dashboard', Dashboard::class)->middleware('can:dashboard.view')->name('portal.dashboard.alias');
    Route::get('/portal/sales', Sales::class)->middleware('can:sales.view_all')->name('portal.sales');
    Route::get('/portal/trash', SampahTransaksi::class)->middleware('can:sales.restore')->name('portal.trash');
    Route::get('/portal/inventories', Inventories::class)->middleware('can:inventory.view')->name('portal.inventories');
    Route::get('/portal/inventory-movements', InventoryMovements::class)->middleware('can:inventory.view')->name('portal.inventory-movements');
    Route::get('/portal/stock-opname', StockOpname::class)->middleware('can:stock_opname.view')->name('portal.stock-opname');
    Route::get('/portal/products', Products::class)->middleware('can:product.view')->name('portal.products');
    Route::get('/portal/categories', Categories::class)->middleware('can:product.view')->name('portal.categories');
    Route::get('/portal/brands', Brands::class)->middleware('can:product.view')->name('portal.brands');
    Route::get('/portal/balances', Balances::class)->middleware('can:balance.view')->name('portal.balances');
    Route::get('/portal/reports/{type?}', Reports::class)->middleware('can:report.sales.view')->name('portal.reports');
    Route::get('/portal/settings/{section?}', Settings::class)->middleware('can:settings.manage')->name('portal.settings');

    // Backward compatibility aliases for operational admin routes
    Route::get('/admin/dashboard', Dashboard::class)->middleware('can:dashboard.view')->name('admin.dashboard');
    Route::get('/admin/sales', Sales::class)->middleware('can:sales.view_all')->name('admin.sales');
    Route::get('/admin/trash', SampahTransaksi::class)->middleware('can:sales.restore')->name('admin.trash');
    Route::get('/admin/inventories', Inventories::class)->middleware('can:inventory.view')->name('admin.inventories');
    Route::get('/admin/inventory-movements', InventoryMovements::class)->middleware('can:inventory.view')->name('admin.inventory-movements');
    Route::get('/admin/stock-opname', StockOpname::class)->middleware('can:stock_opname.view')->name('admin.stock-opname');
    Route::get('/admin/products', Products::class)->middleware('can:product.view')->name('admin.products');
    Route::get('/admin/categories', Categories::class)->middleware('can:product.view')->name('admin.categories');
    Route::get('/admin/brands', Brands::class)->middleware('can:product.view')->name('admin.brands');
    Route::get('/admin/balances', Balances::class)->middleware('can:balance.view')->name('admin.balances');
    Route::get('/admin/reports/{type?}', Reports::class)->middleware('can:report.sales.view')->name('admin.reports');
    Route::get('/admin/settings/{section?}', Settings::class)->middleware('can:settings.manage')->name('admin.settings');

    // Receipt Route
    Route::get('/receipt/thermal/{sale}', function (Sale $sale) {
        abort_unless(auth()->user()->can('sales.view_all') || (auth()->user()->can('sales.view_own') && $sale->cashier_id === auth()->id()), 403);
        $sale->load(['cashier', 'items', 'payments.paymentMethod']);

        return view('receipt.thermal', [
            'sale' => $sale,
            'paperWidth' => '58mm',
        ]);
    })->name('receipt.thermal');

    Route::get('/admin/products/template-excel', function () {
        $path = app(ProductImportService::class)->generateExcelTemplate();

        return response()->download($path, 'Template_Import_Produk_RajaPOS.xlsx')->deleteFileAfterSend(true);
    })->middleware('can:product.view')->name('products.template-excel');

    Route::get('/admin/products/export-excel', function () {
        $path = app(ProductImportService::class)->exportProductsToExcel(
            request()->query('category_id'),
            request()->query('type')
        );

        return response()->download($path, 'Export_Master_Produk_RajaPOS_'.date('Ymd_His').'.xlsx')->deleteFileAfterSend(true);
    })->middleware('can:product.view')->name('products.export-excel');
});
