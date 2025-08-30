<?php
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect('/shop') : view('welcome');
});

    // Shopp
    Route::get('/shop', [ShopController::class, 'index'])
    ->middleware(['auth','verified'])
    ->name('shop.index');

    // Carrito
    Route::middleware('auth')->group(function () {
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('cart.checkout');
    Route::post('/checkout/create-order', [CheckoutController::class, 'createOrder'])->name('cart.checkout.create');
    Route::get('/checkout/pay', [CheckoutController::class, 'payForm'])->name('cart.checkout.pay');
    Route::post('/checkout/pay', [CheckoutController::class, 'processFakePayment'])->name('cart.checkout.process');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('cart.checkout.success');
    Route::get('/checkout/failed', [CheckoutController::class, 'failed'])->name('cart.checkout.failed');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('cart.checkout.cancel');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
