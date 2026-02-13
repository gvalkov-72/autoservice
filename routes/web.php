<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\WorkOrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\CompanySettingController;

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
   АДМИНИСТРАЦИЯ
--------------------------------------------------------------------------*/
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::middleware(['can:admin'])->group(function () {
            Route::resource('users', UserController::class);
            Route::resource('roles', RoleController::class);
            Route::resource('permissions', PermissionController::class);
        });

        // Search (live търсене)
        Route::get('work-orders/search', [WorkOrderController::class, 'search'])->name('work-orders.search');

        /* ---------------- WORK ORDERS ---------------- */
        Route::get('work-orders', [WorkOrderController::class, 'index'])->name('work-orders.index');
        Route::get('work-orders/create', [WorkOrderController::class, 'create'])->name('work-orders.create');
        Route::get('work-orders/{work_order}', [WorkOrderController::class, 'show'])->name('work-orders.show');
        Route::get('work-orders/{work_order}/edit', [WorkOrderController::class, 'edit'])->name('work-orders.edit');
        Route::get('work-orders/{work_order}/print', [WorkOrderController::class, 'print'])->name('work-orders.print');
        Route::get('/work-orders/{work_order}/pdf', [WorkOrderController::class, 'pdf'])->name('work-orders.pdf');

        Route::post('work-orders', [WorkOrderController::class, 'store'])->name('work-orders.store');
        Route::put('work-orders/{work_order}', [WorkOrderController::class, 'update'])->name('work-orders.update');
        Route::delete('work-orders/{work_order}', [WorkOrderController::class, 'destroy'])->name('work-orders.destroy');

        // МПС-та на клиент (за работни поръчки)
        Route::get('/customers/{customer}/vehicles', function (\App\Models\Customer $customer) {
            return response()->json([
                'success' => true,
                'vehicles' => $customer->vehicles()
                    ->where('is_active', true)
                    ->orderBy('vehicle')
                    ->get(['id', 'vehicle', 'plate_number', 'chassis_number', 'last_mileage'])
            ]);
        })->name('customers.vehicles');

        // Търсене на клиенти (Select2) – използва се от работни поръчки
        Route::get('customers/search', [CustomerController::class, 'search'])->name('customers.search');

        // ⚡ LIVE ТЪРСЕНЕ – за списъка с клиенти (връща partials/rows)
        Route::get('customers/live-search', [CustomerController::class, 'liveSearch'])->name('customers.live-search');

        // Търсене на продукти
        Route::get('products/search', [\App\Http\Controllers\Admin\ProductController::class, 'search'])->name('products.search');

        /* ---------------- CUSTOMERS ---------------- */
        Route::resource('customers', CustomerController::class)->except(['show']);
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        Route::get('customers/{customer}/print', [CustomerController::class, 'print'])->name('customers.print');
        Route::get('customers/{customer}/pdf', [CustomerController::class, 'pdf'])->name('customers.pdf');

        /* ---------------- INVOICES ---------------- */
        Route::get('invoices/live-search', [InvoiceController::class, 'liveSearch'])->name('invoices.live-search');
        Route::resource('invoices', InvoiceController::class)->except(['show']);
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

        /* ---------------- COMPANY SETTINGS ---------------- */
        Route::get('company-settings/live-search', [CompanySettingController::class, 'liveSearch'])->name('company-settings.live-search');
        Route::resource('company-settings', CompanySettingController::class)->except(['show']);
        Route::get('company-settings/{company_setting}', [CompanySettingController::class, 'show'])->name('company-settings.show');
        Route::get('company-settings/{company_setting}/print', [CompanySettingController::class, 'print'])->name('company-settings.print');
        Route::get('company-settings/{company_setting}/pdf', [CompanySettingController::class, 'pdf'])->name('company-settings.pdf');
    });

require __DIR__ . '/auth.php';
