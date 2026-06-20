<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
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
    Route::apiResource('products', \App\Http\Controllers\ProductController::class);
    Route::apiResource('categories', \App\Http\Controllers\CategoryController::class);
    Route::apiResource('addresses', \App\Http\Controllers\UserAddressController::class);
    Route::patch('orders/{id}/status', [\App\Http\Controllers\OrderController::class, 'updateStatus']);
    Route::apiResource('orders', \App\Http\Controllers\OrderController::class);
    Route::get('cart', [\App\Http\Controllers\CartController::class, 'index']);
    Route::post('cart', [\App\Http\Controllers\CartController::class, 'store']);
    Route::delete('cart/items/{id}', [\App\Http\Controllers\CartController::class, 'destroy']);
});
