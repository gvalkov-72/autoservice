<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use App\Models\Payment;
use App\Observers\PaymentObserver;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // 1. Пренасочваме "/" → login
        Route::get('/', fn () => redirect()->route('login'));

        // 2. След успешен login → Admin панел
        Route::get('/dashboard', fn () => redirect('/admin/customers'));

        Payment::observe(PaymentObserver::class);
    }

    public function register(): void
    {
        //
    }
}