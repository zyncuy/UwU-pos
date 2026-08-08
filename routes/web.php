<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Route Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route Barang / Produk
    Route::resource('products', ProductController::class);
    Route::patch('/products/{product}/update-stock', [ProductController::class, 'updateStock'])->name('products.updateStock');

    // Route Kategori
    Route::resource('categories', CategoryController::class);
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

    // Route Transaksi Kasir
    Route::resource('transactions', TransactionController::class);
});

require __DIR__.'/auth.php';