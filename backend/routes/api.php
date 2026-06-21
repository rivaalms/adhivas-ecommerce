<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::middleware('auth:api')->group(function () {
        Route::post('refresh', 'refresh');
        Route::post('logout', 'logout');
    });
});

Route::middleware('auth:api')->group(function () {
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('dashboard/status-breakdown', [DashboardController::class, 'statusBreakdown']);
    Route::get('dashboard/sales-trend', [DashboardController::class, 'salesTrend']);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('addresses', UserAddressController::class);
    Route::get('users', [UserController::class, 'index']);
    Route::patch('orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::apiResource('orders', OrderController::class);
    Route::get('cart', [CartController::class, 'index']);
    Route::post('cart', [CartController::class, 'store']);
    Route::delete('cart/items/{id}', [CartController::class, 'destroy']);
});
