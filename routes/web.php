<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
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
    Route::get('/pos', function () {
        return view('pos.index');
    })->name('pos.index');

    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/checkout', function () {
            return view('pos.checkout');
        })->name('checkout');

        Route::get('/receipt/{id}', function ($id) {
            return view('pos.receipt', compact('id'));
        })->name('receipt');
    });
});

require __DIR__.'/auth.php';
