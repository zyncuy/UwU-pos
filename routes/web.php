<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', ProductController::class);
    Route::patch('/products/{product}/update-stock', [ProductController::class, 'updateStock'])->name('products.updateStock');

    Route::resource('categories', CategoryController::class);
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
});