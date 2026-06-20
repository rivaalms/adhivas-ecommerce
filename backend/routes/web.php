<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth:web'])->group(function () {
    Route::inertia('test', 'Welcome')->name('auth');
});
