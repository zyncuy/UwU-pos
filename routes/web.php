<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth'])->group(function () {

    // Route Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin', [DashboardController::class, 'index'])->middleware('role:Admin')->name('admin.dashboard');
    Route::get('/owner', [DashboardController::class, 'index'])->middleware('role:Owner')->name('owner.dashboard');
    Route::get('/kasir', [DashboardController::class, 'index'])->middleware('role:Kasir')->name('kasir.dashboard');

    // Master Data Kategori & Produk
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);

    // Transaksi Kasir POS
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');

    // Route Profile Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';