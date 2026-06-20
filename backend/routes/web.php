<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app' => config('app.name'),
        'status' => 'OK',
        'api_version' => '1.0.0',
    ]);
})->name('home');
