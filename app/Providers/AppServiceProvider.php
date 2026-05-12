<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::bind('cuenta', fn (string $value) => Account::query()->findOrFail($value));
        Route::bind('categoria', fn (string $value) => Category::query()->findOrFail($value));
        Route::bind('movimiento', fn (string $value) => Transaction::query()->findOrFail($value));
    }
}
