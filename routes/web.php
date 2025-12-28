<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Тук се дефинират всички маршрути за уеб приложението. Тези маршрути
| се зареждат от RouteServiceProvider в рамките на група, която съдържа
| "web" middleware групата. Сега създайте нещо прекрасно!
|
*/

// Начална страница - пренасочва към логин
Route::get('/', fn() => redirect()->route('login'));

// Дашборд - основен панел след логин
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

    /* --------------------------------------------------------------------------
       МАРШРУТИ ЗА ПРОДУКТИ (Products)
       --------------------------------------------------------------------------
       Основни CRUD операции за управление на продуктите в склада
       Допълнителни маршрути за експорт, импорт и баркод функционалности
    */
    Route::prefix('products')->name('products.')->group(function () {
        // Основни CRUD маршрути
        Route::get('/', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ProductController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [\App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'update'])->name('update');
        Route::patch('/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'update']); // Алтернативен маршрут за PATCH
        Route::delete('/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('destroy');

        // Експорт на продукти
        Route::get('/export/all', [\App\Http\Controllers\Admin\ProductController::class, 'exportAll'])
            ->name('export.all');
        Route::post('/export/selected', [\App\Http\Controllers\Admin\ProductController::class, 'exportSelected'])
            ->name('export.selected');

        // Баркод функционалности
        Route::get('/{product}/barcode', [\App\Http\Controllers\Admin\ProductController::class, 'barcode'])
            ->name('barcode');

        // Импорт на продукти
        Route::get('/import', [\App\Http\Controllers\Admin\ProductController::class, 'import'])
            ->name('import');
        Route::post('/import/process', [\App\Http\Controllers\Admin\ProductController::class, 'processImport'])
            ->name('import.process');

        // Bulk операции (масови действия)
        Route::post('/bulk-actions', [\App\Http\Controllers\Admin\ProductController::class, 'bulkActions'])
            ->name('bulk.actions');
    });

    // Legacy resource маршрут за обратна съвместимост (ако е необходим)
    // Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);

    Route::resource('services',    \App\Http\Controllers\Admin\ServiceController::class);
    Route::resource('service-categories', \App\Http\Controllers\Admin\ServiceCategoryController::class);
    Route::resource('work-orders', \App\Http\Controllers\Admin\WorkOrderController::class);

    // ДОБАВЕН МАРШРУТ ЗА ЕКСПОРТ НА WORK ORDERS
    Route::get('work-orders/{workOrder}/export/{type}', [\App\Http\Controllers\Admin\WorkOrderController::class, 'export'])
        ->name('work-orders.export');

    Route::resource('invoices',    \App\Http\Controllers\Admin\InvoiceController::class);

    /* --- ДАННИ НА АВТОСЕРВИЗА (Company Settings) --- */
    Route::resource('company-settings', \App\Http\Controllers\Admin\CompanySettingController::class);

    /* --- EXPORT-и за Company Settings --- */
    Route::get('company-settings/{companySetting}/export/pdf', [\App\Http\Controllers\Admin\CompanySettingController::class, 'exportPdf'])
        ->name('company-settings.export.pdf');
    Route::get('company-settings/{companySetting}/export/excel', [\App\Http\Controllers\Admin\CompanySettingController::class, 'exportExcel'])
        ->name('company-settings.export.excel');
    Route::get('company-settings/{companySetting}/export/csv', [\App\Http\Controllers\Admin\CompanySettingController::class, 'exportCsv'])
        ->name('company-settings.export.csv');

    /* --- EXPORT-и за Customer --- */
    Route::get('customers/{customer}/export/pdf', [\App\Http\Controllers\Admin\CustomerController::class, 'exportPdf'])
        ->name('customers.export.pdf');
    Route::get('customers/{customer}/export/excel', [\App\Http\Controllers\Admin\CustomerController::class, 'exportExcel'])
        ->name('customers.export.excel');
    Route::get('customers/{customer}/export/csv', [\App\Http\Controllers\Admin\CustomerController::class, 'exportCsv'])
        ->name('customers.export.csv');

    /* --- ДОБАВЕНИ МАРШРУТИ ЗА ДОПЪЛНИТЕЛНИ ФУНКЦИИ НА CUSTOMERS --- */
    Route::post('customers/bulk-action', [\App\Http\Controllers\Admin\CustomerController::class, 'bulkAction'])->name('customers.bulk-action');
    Route::get('customers/export/all', [\App\Http\Controllers\Admin\CustomerController::class, 'exportAll'])->name('customers.export.all');
    Route::get('customers/import', [\App\Http\Controllers\Admin\CustomerController::class, 'import'])->name('customers.import');
    Route::post('customers/import', [\App\Http\Controllers\Admin\CustomerController::class, 'importStore'])->name('customers.import.store');

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

/* --------------------------------------------------------------------------
   АВТЕНТИКАЦИЯ
   Маршрути за вход, регистрация, потвърждение на имейл, нулиране на парола
--------------------------------------------------------------------------*/
require __DIR__ . '/auth.php';