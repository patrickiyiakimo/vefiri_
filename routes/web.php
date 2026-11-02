<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VendorApplicationController as AdminVendorApplicationController;
use App\Http\Controllers\Admin\VendorController as AdminVendorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/vendors', [VendorController::class, 'index'])->name('vendors.index');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/items', [CartController::class, 'store'])->name('cart.items.store');
    Route::patch('/cart/items/{product}', [CartController::class, 'update'])->name('cart.items.update');
    Route::delete('/cart/items/{product}', [CartController::class, 'destroy'])->name('cart.items.destroy');

    Route::get('/favourites', [FavouriteController::class, 'index'])->name('favourites.index');
    Route::post('/favourites', [FavouriteController::class, 'store'])->name('favourites.store');
    Route::delete('/favourites/{product}', [FavouriteController::class, 'destroy'])->name('favourites.destroy');

    Route::get('/vendors/apply', [VendorController::class, 'apply'])->name('vendors.apply');
    Route::post('/vendors/apply', [VendorController::class, 'store'])->name('vendors.apply.store');
    Route::get('/vendors/application', [VendorController::class, 'status'])->name('vendors.status');
});

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/vendors', [AdminVendorController::class, 'index'])->name('vendors.index');
        Route::get('/vendor-applications', [AdminVendorApplicationController::class, 'index'])->name('applications.index');
        Route::patch('/vendor-applications/{vendorApplication}/approve', [AdminVendorApplicationController::class, 'approve'])->name('applications.approve');
        Route::patch('/vendor-applications/{vendorApplication}/reject', [AdminVendorApplicationController::class, 'reject'])->name('applications.reject');
    });
