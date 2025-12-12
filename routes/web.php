<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

    /* --- АДМИНИСТРАЦИЯ (само admin) --- */
    Route::middleware(['can:admin'])->group(function () {
        Route::resource('users',       \App\Http\Controllers\Admin\UserController::class);
        Route::resource('roles',       \App\Http\Controllers\Admin\RoleController::class);
        Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class);
    });

    /* --- ОСНОВНИ МОДУЛИ --- */
    Route::resource('customers',   \App\Http\Controllers\Admin\CustomerController::class);
    Route::resource('vehicles',    \App\Http\Controllers\Admin\VehicleController::class);
    Route::resource('products',    \App\Http\Controllers\Admin\ProductController::class);
    Route::resource('services',    \App\Http\Controllers\Admin\ServiceController::class);
    Route::resource('service-categories', \App\Http\Controllers\Admin\ServiceCategoryController::class);
    Route::resource('work-orders', \App\Http\Controllers\Admin\WorkOrderController::class);
    Route::resource('invoices',    \App\Http\Controllers\Admin\InvoiceController::class);

    /* --- EXPORT-и за Customer --- */
    Route::get('customers/{customer}/export/pdf', [\App\Http\Controllers\Admin\CustomerController::class, 'exportPdf'])
        ->name('customers.export.pdf');
    Route::get('customers/{customer}/export/excel', [\App\Http\Controllers\Admin\CustomerController::class, 'exportExcel'])
        ->name('customers.export.excel');
    Route::get('customers/{customer}/export/csv', [\App\Http\Controllers\Admin\CustomerController::class, 'exportCsv'])
        ->name('customers.export.csv');

    /* --- EXPORT-и за Vehicle --- */
    Route::get('vehicles/{vehicle}/export/pdf', [\App\Http\Controllers\Admin\VehicleController::class, 'exportPdf'])->name('vehicles.export.pdf');
    Route::get('vehicles/{vehicle}/export/excel', [\App\Http\Controllers\Admin\VehicleController::class, 'exportExcel'])->name('vehicles.export.excel');
    Route::get('vehicles/{vehicle}/export/csv', [\App\Http\Controllers\Admin\VehicleController::class, 'exportCsv'])->name('vehicles.export.csv');

    /* --- СКЛАД --- */
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\StockController::class, 'index'])->name('index');
        Route::get('/create-purchase', [\App\Http\Controllers\Admin\StockController::class, 'createPurchase'])->name('create-purchase');
        Route::post('/store-purchase', [\App\Http\Controllers\Admin\StockController::class, 'storePurchase'])->name('store-purchase');
        Route::get('/create-adjustment', [\App\Http\Controllers\Admin\StockController::class, 'createAdjustment'])->name('create-adjustment');
        Route::post('/store-adjustment', [\App\Http\Controllers\Admin\StockController::class, 'storeAdjustment'])->name('store-adjustment');
    });

    /* --- ПЛАЩАНИЯ --- */
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\PaymentController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\PaymentController::class, 'store'])->name('store');
        Route::get('/{payment}/pdf', [\App\Http\Controllers\Admin\PaymentController::class, 'pdf'])->name('pdf');
    });

    /* --- БАРКОД / PLU --- */
    Route::get('barcode/{code}.png', function (string $code) {
        return response(\App\Support\BarcodeHelper::png($code))->header('Content-Type', 'image/png');
    })->name('barcode.png');

    Route::get('products/{product}/barcode', [\App\Http\Controllers\Admin\ProductController::class, 'barcode'])
        ->name('products.barcode');

    Route::get('api/product-by-sku/{sku}', function (string $sku) {
        return \App\Models\Product::where('sku', $sku)->first() ?? [];
    })->name('api.product-by-sku');

    /* --- AJAX API за Work Orders --- */
    Route::prefix('api')->name('api.')->group(function () {
        // Търсене на клиенти и автомобили за autocomplete
        Route::get('search/customer-vehicle', [\App\Http\Controllers\Admin\WorkOrderController::class, 'search'])
            ->name('search.customer-vehicle');
        
        // Информация за клиент
        Route::get('customer-info/{customer}', [\App\Http\Controllers\Admin\WorkOrderController::class, 'customerInfo'])
            ->name('customer-info');
        
        // Информация за автомобил
        Route::get('vehicle-info/{vehicle}', [\App\Http\Controllers\Admin\WorkOrderController::class, 'vehicleInfo'])
            ->name('vehicle-info');
        
        // Автомобили на клиент
        Route::get('customer-vehicles/{customer}', [\App\Http\Controllers\Admin\WorkOrderController::class, 'customerVehicles'])
            ->name('customer-vehicles');
        
        // Autocomplete за продукти и услуги
        Route::get('products/autocomplete', [\App\Http\Controllers\Admin\WorkOrderController::class, 'productsAutocomplete'])
            ->name('products.autocomplete');
    });

    /* --- Стар AJAX endpoint (трябва да го преименуваме или премахнем) --- */
    Route::get('api/customer-vehicles-legacy/{customer}', function (\App\Models\Customer $customer) {
        return $customer->vehicles()->select('id', 'plate', 'make', 'model')->get();
    })->name('api.customer-vehicles.legacy');

    /* --- PDF --- */
    Route::get('work-orders/{workOrder}/pdf', [\App\Http\Controllers\Admin\WorkOrderController::class, 'pdf'])
        ->name('work-orders.pdf');
        
    /* --- Търсене за Work Orders (за обратна съвместимост) --- */
    Route::get('work-orders/search', [\App\Http\Controllers\Admin\WorkOrderController::class, 'search'])
        ->name('work-orders.search');
});

require __DIR__ . '/auth.php';