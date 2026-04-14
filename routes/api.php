<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileSiswaController;
use App\Http\Controllers\Api\MobileDokumenController;

Route::prefix('mobile')->group(function () {
    Route::post('/login', [MobileAuthController::class , 'login']);

    Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [MobileAuthController::class , 'logout']);
            Route::get('/me', [MobileSiswaController::class , 'me']);
            Route::put('/profil', [MobileSiswaController::class , 'updateProfil']);
            Route::get('/nilai', [MobileSiswaController::class , 'nilai']);
            Route::get('/rekomendasi', [MobileSiswaController::class , 'rekomendasi']);
            Route::get('/rekomendasi/pdf', [MobileSiswaController::class , 'rekomendasiPdf']);
            Route::get('/ping', fn() => response()->json(['ok' => true]));

            // Dokumen siswa
            Route::get('/dokumen', [MobileDokumenController::class , 'index']);
            Route::post('/dokumen', [MobileDokumenController::class , 'store']);
            Route::put('/dokumen/{id}', [MobileDokumenController::class , 'update']);
            Route::delete('/dokumen/{id}', [MobileDokumenController::class , 'destroy']);
        }
        );    });
