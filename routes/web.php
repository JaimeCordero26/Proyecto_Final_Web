<?php
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Redirigir a shop si está autenticado, sino mostrar welcome
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/shop');
    }
    return view('welcome');
});

// Rutas del carrito
Route::post('/cart/add/{product}', [CartController::class, 'add'])
    ->middleware(['auth'])
    ->name('cart.add');

Route::get('/cart', [CartController::class, 'index'])
    ->middleware(['auth'])
    ->name('cart.index');

Route::delete('/cart/{product}', [CartController::class, 'remove'])
    ->middleware(['auth'])
    ->name('cart.remove');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/checkout', [CheckoutController::class, 'index'])
    ->middleware(['auth'])
    ->name('checkout.index');

Route::post('/checkout/pay', [CheckoutController::class, 'pay'])
    ->middleware(['auth'])
    ->name('checkout.pay');





Route::get('/shop', [ShopController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('shop.index'); 

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';