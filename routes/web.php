<?php

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
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
Route::post('/prodotti/{product:slug}/recensioni', [ReviewController::class, 'store'])->name('reviews.store')->middleware('auth');
Route::get('/servizi', [ServiceController::class, 'index'])->name('services');
Route::get('/servizi/{slug}', [ServiceController::class, 'show'])->name('services.show');

/*
|--------------------------------------------------------------------------
| Carrello
|--------------------------------------------------------------------------
*/
Route::get('/carrello', [CartController::class, 'index'])->name('cart.index');
Route::get('/carrello/anteprima', [CartController::class, 'preview'])->name('cart.preview');
Route::post('/carrello', [CartController::class, 'add'])->name('cart.add');
Route::patch('/carrello/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/carrello/{item}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/carrello', [CartController::class, 'clear'])->name('cart.clear');

/*
|--------------------------------------------------------------------------
| Checkout
|--------------------------------------------------------------------------
*/
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::post('/checkout/sconto/applica', [CheckoutController::class, 'applyDiscount'])->name('checkout.discount.apply');
Route::post('/checkout/sconto/rimuovi', [CheckoutController::class, 'removeDiscount'])->name('checkout.discount.remove');
Route::get('/checkout/stripe/successo', [CheckoutController::class, 'stripeSuccess'])->name('checkout.stripe.success');
Route::get('/checkout/paypal/successo', [CheckoutController::class, 'paypalSuccess'])->name('checkout.paypal.success');
Route::get('/checkout/satispay/successo', [CheckoutController::class, 'satispaySuccess'])->name('checkout.satispay.success');
Route::get('/checkout/successo', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/errore', [CheckoutController::class, 'failed'])->name('checkout.failed');

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

    Route::get('/password/reset', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/password/email', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/password/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Verifica email
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/email/verifica', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verifica/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');
    Route::post('/email/verifica/reinvia', [VerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| Riattivazione account (link firmato, no auth richiesta)
|--------------------------------------------------------------------------
*/
Route::get('/account/riattiva/{id}', [AccountController::class, 'reactivate'])
    ->middleware('signed')
    ->name('account.reactivate');

/*
|--------------------------------------------------------------------------
| Area utente (richiede autenticazione)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::get('/ordini', [AccountController::class, 'orders'])->name('orders');
    Route::get('/ordini/{order:order_number}', [AccountController::class, 'showOrder'])->name('orders.show');
    Route::get('/indirizzi', [AccountController::class, 'addresses'])->name('addresses');
    Route::post('/indirizzi', [AccountController::class, 'storeAddress'])->name('addresses.store');
    Route::delete('/indirizzi/{address}', [AccountController::class, 'destroyAddress'])->name('addresses.destroy');
    Route::patch('/indirizzi/{address}/default', [AccountController::class, 'setDefaultAddress'])->name('addresses.setDefault');
    Route::get('/wishlist', [AccountController::class, 'wishlist'])->name('wishlist');
    Route::post('/wishlist/{product}', [AccountController::class, 'toggleWishlist'])->name('wishlist.toggle');
    Route::get('/profilo', [AccountController::class, 'editProfile'])->name('profile');
    Route::patch('/profilo', [AccountController::class, 'updateProfile'])->name('profile.update');
    Route::delete('/', [AccountController::class, 'destroy'])->name('destroy');
});
