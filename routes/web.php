<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InvoicePdfController;
use App\Http\Controllers\Admin\InvoiceExportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => redirect()->route('login'));

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/* --------------------------------------------------------------------------
   АДМИНИСТРАЦИЯ (с права)
--------------------------------------------------------------------------*/
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    Route::middleware(['can:admin'])->group(function () {
        Route::resource('users',       \App\Http\Controllers\Admin\UserController::class);
        Route::resource('roles',       \App\Http\Controllers\Admin\RoleController::class);
        Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class);
    });

    Route::resource('customers',   \App\Http\Controllers\Admin\CustomerController::class);
    Route::resource('vehicles',    \App\Http\Controllers\Admin\VehicleController::class);

    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ProductController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [\App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'update'])->name('update');
        Route::patch('/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'update']);
        Route::delete('/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('destroy');

        Route::get('/export/all', [\App\Http\Controllers\Admin\ProductController::class, 'exportAll'])->name('export.all');
        Route::post('/export/selected', [\App\Http\Controllers\Admin\ProductController::class, 'exportSelected'])->name('export.selected');

        Route::get('/{product}/barcode', [\App\Http\Controllers\Admin\ProductController::class, 'barcode'])->name('barcode');

        Route::get('/import', [\App\Http\Controllers\Admin\ProductController::class, 'import'])->name('import');
        Route::post('/import/process', [\App\Http\Controllers\Admin\ProductController::class, 'processImport'])->name('import.process');

        Route::post('/bulk-actions', [\App\Http\Controllers\Admin\ProductController::class, 'bulkActions'])->name('bulk.actions');
    });

    Route::resource('services',    \App\Http\Controllers\Admin\ServiceController::class);
    Route::resource('service-categories', \App\Http\Controllers\Admin\ServiceCategoryController::class);
    Route::resource('work-orders', \App\Http\Controllers\Admin\WorkOrderController::class);

    Route::get('work-orders/{workOrder}/export/{type}', [\App\Http\Controllers\Admin\WorkOrderController::class, 'export'])
        ->name('work-orders.export');

    /* ----------------------------------------------------- */

    Route::resource('company-settings', \App\Http\Controllers\Admin\CompanySettingController::class);

    Route::get('company-settings/{companySetting}/export/pdf', [\App\Http\Controllers\Admin\CompanySettingController::class, 'exportPdf'])
        ->name('company-settings.export.pdf');
    Route::get('company-settings/{companySetting}/export/excel', [\App\Http\Controllers\Admin\CompanySettingController::class, 'exportExcel'])
        ->name('company-settings.export.excel');
    Route::get('company-settings/{companySetting}/export/csv', [\App\Http\Controllers\Admin\CompanySettingController::class, 'exportCsv'])
        ->name('company-settings.export.csv');

    Route::get('customers/{customer}/export/pdf', [\App\Http\Controllers\Admin\CustomerController::class, 'exportPdf'])
        ->name('customers.export.pdf');
    Route::get('customers/{customer}/export/excel', [\App\Http\Controllers\Admin\CustomerController::class, 'exportExcel'])
        ->name('customers.export.excel');
    Route::get('customers/{customer}/export/csv', [\App\Http\Controllers\Admin\CustomerController::class, 'exportCsv'])
        ->name('customers.export.csv');

    Route::post('customers/bulk-action', [\App\Http\Controllers\Admin\CustomerController::class, 'bulkAction'])
        ->name('customers.bulk-action');
    Route::get('customers/export/all', [\App\Http\Controllers\Admin\CustomerController::class, 'exportAll'])
        ->name('customers.export.all');
    Route::get('customers/import', [\App\Http\Controllers\Admin\CustomerController::class, 'import'])
        ->name('customers.import');
    Route::post('customers/import', [\App\Http\Controllers\Admin\CustomerController::class, 'importStore'])
        ->name('customers.import.store');

    Route::get('vehicles/{vehicle}/export/pdf', [\App\Http\Controllers\Admin\VehicleController::class, 'exportPdf'])
        ->name('vehicles.export.pdf');
    Route::get('vehicles/{vehicle}/export/excel', [\App\Http\Controllers\Admin\VehicleController::class, 'exportExcel'])
        ->name('vehicles.export.excel');
    Route::get('vehicles/{vehicle}/export/csv', [\App\Http\Controllers\Admin\VehicleController::class, 'exportCsv'])
        ->name('vehicles.export.csv');

    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\StockController::class, 'index'])->name('index');
        Route::get('/create-purchase', [\App\Http\Controllers\Admin\StockController::class, 'createPurchase'])->name('create-purchase');
        Route::post('/store-purchase', [\App\Http\Controllers\Admin\StockController::class, 'storePurchase'])->name('store-purchase');
        Route::get('/create-adjustment', [\App\Http\Controllers\Admin\StockController::class, 'createAdjustment'])->name('create-adjustment');
        Route::post('/store-adjustment', [\App\Http\Controllers\Admin\StockController::class, 'storeAdjustment'])->name('store-adjustment');
    });

    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\PaymentController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\PaymentController::class, 'store'])->name('store');
        Route::get('/{payment}/pdf', [\App\Http\Controllers\Admin\PaymentController::class, 'pdf'])->name('pdf');
    });

    Route::get('barcode/{code}.png', function (string $code) {
        return response(\App\Support\BarcodeHelper::png($code))
            ->header('Content-Type', 'image/png');
    })->name('barcode.png');

    Route::get('work-orders/{workOrder}/pdf', [\App\Http\Controllers\Admin\WorkOrderController::class, 'pdf'])
        ->name('work-orders.pdf');

    Route::get('work-orders/search', [\App\Http\Controllers\Admin\WorkOrderController::class, 'search'])
        ->name('work-orders.search');

    /* -------------------- INVOICE PDF -------------------- */

    // ПЪРВО: специфичният маршрут за експорт
    Route::get('invoices/export/pdf', [InvoiceExportController::class, 'exportPdf'])
        ->name('invoices.export.pdf');

    // ВТОРО: маршрутът за единична фактура PDF
    Route::get('invoices/{invoice}/pdf', [InvoicePdfController::class, 'show'])
        ->name('invoices.pdf');

    // ТРЕТО: ресурсният маршрут (който е най-общ)
    Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class);
});

require __DIR__ . '/auth.php';
