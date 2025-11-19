<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Public)
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes (Require Auth)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::prefix('procurement')->name('procurement.')->group(function () {

        // GET /procurement/create
        Route::get('/create', [ProcurementController::class, 'create'])
            ->name('create');

        // POST /procurement/store
        Route::post('/store', [ProcurementController::class, 'store'])
            ->name('store');
        Route::get('/suppliers', [ProcurementController::class, 'supplierIndex'])->name('supplier.index');
        Route::get('/products', [ProcurementController::class, 'productIndex'])->name('product.index');
        Route::get('/products/{product}/edit', [ProcurementController::class, 'edit'])->name('product.edit');
        Route::put('/products/{product}', [ProcurementController::class, 'update'])->name('product.update');
        Route::delete('/products/{product}', [ProcurementController::class, 'destroy'])->name('product.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Default Redirect
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});
