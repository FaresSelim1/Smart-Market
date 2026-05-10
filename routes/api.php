<?php

use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderApiController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/orders', [OrderApiController::class, 'index']);
    Route::post('/orders', [OrderApiController::class, 'store']);
});