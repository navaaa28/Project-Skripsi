<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileSiswaController;

Route::prefix('mobile')->group(function () {
    Route::post('/login', [MobileAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [MobileAuthController::class, 'logout']);
        Route::get('/me', [MobileSiswaController::class, 'me']);
        Route::get('/nilai', [MobileSiswaController::class, 'nilai']);
        Route::get('/rekomendasi', [MobileSiswaController::class, 'rekomendasi']);
        Route::get('/rekomendasi/pdf', [MobileSiswaController::class, 'rekomendasiPdf']);
    });
});
