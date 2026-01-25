<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\WorkOrderController;
use App\Http\Controllers\Admin\CustomerController;

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
        Route::get(
            'work-orders/search',
            [WorkOrderController::class, 'search']
        )->name('work-orders.search');

        /* ---------------- WORK ORDERS ---------------- */
        // GET пътища
        Route::get('work-orders', [WorkOrderController::class, 'index'])
            ->name('work-orders.index');

        Route::get('work-orders/create', [WorkOrderController::class, 'create'])
            ->name('work-orders.create');

        Route::get('work-orders/{work_order}', [WorkOrderController::class, 'show'])
            ->name('work-orders.show');

        Route::get('work-orders/{work_order}/edit', [WorkOrderController::class, 'edit'])
            ->name('work-orders.edit');

        // POST/PUT/DELETE пътища
        Route::post('work-orders', [WorkOrderController::class, 'store'])
            ->name('work-orders.store');

        Route::put('work-orders/{work_order}', [WorkOrderController::class, 'update'])
            ->name('work-orders.update');

        Route::delete('work-orders/{work_order}', [WorkOrderController::class, 'destroy'])
            ->name('work-orders.destroy');

        // В routes/web.php, след work orders пътищата, добавете:
        Route::get('customers/search', [CustomerController::class, 'search'])
            ->name('customers.search');
        
    });

require __DIR__ . '/auth.php';
