<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserAddressController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/cart', 'welcome');
Route::view('/checkout', 'welcome');
Route::view('/profile/addresses', 'welcome');
Route::view('/admin/{any?}', 'welcome')->where('any', '.*');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

    Route::post('/register/otp', OtpController::class)->name('register.otp');

    Route::get('/auth/google', [SocialLoginController::class, 'redirect'])->name('oauth.google.redirect');
    Route::get('/auth/google/callback', [SocialLoginController::class, 'callback'])->name('oauth.google.callback');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// API Routes for JSON-based authentication
Route::prefix('api')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'apiStore'])->name('api.register');
    Route::post('/login', [AuthenticatedSessionController::class, 'apiStore'])->name('api.login');
    Route::post('/logout', [AuthenticatedSessionController::class, 'apiDestroy'])
        ->middleware('auth')
        ->name('api.logout');
    Route::get('/user', function () {
        return response()->json(Auth::user());
    })->middleware('auth')->name('api.user');

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);

    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::patch('/cart/{cartItem}', [CartController::class, 'update']);
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy']);
    Route::post('/cart/claim', [CartController::class, 'claim'])->middleware('auth');

    Route::middleware('auth')->group(function () {
        Route::get('/addresses', [UserAddressController::class, 'index']);
        Route::post('/addresses', [UserAddressController::class, 'store']);
        Route::put('/addresses/{userAddress}', [UserAddressController::class, 'update']);
        Route::delete('/addresses/{userAddress}', [UserAddressController::class, 'destroy']);
        Route::post('/addresses/{userAddress}/default', [UserAddressController::class, 'setDefault']);

        Route::get('/checkout', [CheckoutController::class, 'show']);
        Route::post('/checkout', [CheckoutController::class, 'store']);
    });

    Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', AdminDashboardController::class);

        Route::get('/categories', [AdminCategoryController::class, 'index']);
        Route::post('/categories', [AdminCategoryController::class, 'store']);
        Route::put('/categories/{category}', [AdminCategoryController::class, 'update']);
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy']);

        Route::get('/products', [AdminProductController::class, 'index']);
        Route::post('/products', [AdminProductController::class, 'store']);
        Route::put('/products/{product}', [AdminProductController::class, 'update']);
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy']);

        Route::get('/orders', [AdminOrderController::class, 'index']);
        Route::put('/orders/{order}', [AdminOrderController::class, 'update']);
    });
});
