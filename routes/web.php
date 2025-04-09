<?php

use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Product CRUD Routes with 'products' prefix
Route::prefix('products')
    ->controller(ProductController::class)
    ->name('products.')
    ->group(function () {
        // Standard CRUD Routes
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{product}', 'show')->name('show');
        Route::get('/{product}/edit', 'edit')->name('edit');
        Route::put('/{product}', 'update')->name('update');
        Route::delete('/{product}', 'destroy')->name('destroy');

        // Stock Management Routes (nested under product ID)
        Route::prefix('/{product}')->group(function () {
            // Add Stock Form and Action
            Route::get('/add-stock', [ProductController::class, 'showAddStockForm'])->name('add-stock.form');
            Route::post('/add-stock', [ProductController::class, 'addStock'])->name('add-stock');

            // Use Stock Form and Action
            Route::get('/use-stock', [ProductController::class, 'showUseStockForm'])->name('use-stock.form');
            Route::post('/use-stock', [ProductController::class, 'useStock'])->name('use-stock');
        });

    });

Route::prefix('transactions')
    ->controller(InventoryTransactionController::class)
    ->name('transactions.')
    ->group(function () {
        // Main Transactions List with Filters
        Route::get('/', 'index')->name('index');

        // Data Export
        Route::get('/export', 'export')->name('export');

        // Analytics Dashboard
        Route::get('/dashboard', 'dashboard')->name('dashboard');

        // If you add these later:
        // Route::get('/summary', 'summary')->name('summary');
        // Route::get('/{transaction}', 'show')->name('show');
    });

