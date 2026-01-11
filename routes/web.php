<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes - only accessible by admin role
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Category CRUD routes
    Route::resource('categories', CategoryController::class);

    // Product CRUD routes
    Route::resource('products', ProductController::class);
});

// Cashier routes - only accessible by cashier role
Route::middleware(['auth', 'role:cashier'])->group(function () {
    Route::get('/pos', [CartController::class, 'index'])->name('pos.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update/{productId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{productId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/pos/checkout', [CartController::class, 'checkout'])->name('pos.checkout');
    Route::post('/pos/complete-checkout', [CartController::class, 'completeCheckout'])->name('pos.complete-checkout');
    Route::get('/order/{order}/receipt', [CartController::class, 'receipt'])->name('order.receipt');
});

require __DIR__.'/auth.php';
