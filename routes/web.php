<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('cuentas', AccountController::class);
    Route::resource('categorias', CategoryController::class);
    Route::get('/ingresos', [IncomeController::class, 'index'])->name('ingresos.index');
    Route::get('/ingresos/nuevo', [IncomeController::class, 'create'])->name('ingresos.create');
    Route::post('/ingresos', [IncomeController::class, 'store'])->name('ingresos.store');
    Route::resource('movimientos', TransactionController::class);

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/usuarios', [UserAdminController::class, 'index'])->name('usuarios.index');
        Route::patch('/usuarios/{user}/rol', [UserAdminController::class, 'updateRole'])->name('usuarios.update-role');
    });
});

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
