<?php

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Pagine pubbliche
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/prodotti', [ProductController::class, 'index'])->name('products.index');
Route::get('/prodotti/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/servizi', [ServiceController::class, 'index'])->name('services');
Route::get('/servizi/{slug}', [ServiceController::class, 'show'])->name('services.show');

/*
|--------------------------------------------------------------------------
| Carrello (accessibile anche agli ospiti)
|--------------------------------------------------------------------------
*/
Route::get('/carrello', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrello', [CartController::class, 'add'])->name('cart.add');
Route::patch('/carrello/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/carrello/{item}', [CartController::class, 'remove'])->name('cart.remove');

/*
|--------------------------------------------------------------------------
| Checkout (richiede autenticazione)
|--------------------------------------------------------------------------
*/
Route::get('/checkout',          [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout',         [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/successo', [CheckoutController::class, 'success'])->name('checkout.success');

/*
|--------------------------------------------------------------------------
| Autenticazione
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Area utente (richiede autenticazione)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::get('/ordini', [AccountController::class, 'orders'])->name('orders');
    Route::get('/ordini/{order}', [AccountController::class, 'showOrder'])->name('orders.show');
    Route::get('/indirizzi', [AccountController::class, 'addresses'])->name('addresses');
    Route::post('/indirizzi', [AccountController::class, 'storeAddress'])->name('addresses.store');
    Route::delete('/indirizzi/{address}', [AccountController::class, 'destroyAddress'])->name('addresses.destroy');
    Route::get('/wishlist', [AccountController::class, 'wishlist'])->name('wishlist');
    Route::post('/wishlist/{product}', [AccountController::class, 'toggleWishlist'])->name('wishlist.toggle');
    Route::get('/profilo', [AccountController::class, 'editProfile'])->name('profile');
    Route::patch('/profilo', [AccountController::class, 'updateProfile'])->name('profile.update');
});
