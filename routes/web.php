<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\GuruDashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileSiswaController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
})->middleware('auth');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('guru/import', [GuruController::class, 'import'])->name('guru.import');
    Route::resource('guru', GuruController::class);
    Route::post('siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
    Route::resource('siswa', SiswaController::class);
    Route::resource('kelas', KelasController::class);
    Route::resource('mapel', MapelController::class);
    Route::resource('users', UserController::class);
});

Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
    Route::get('/siswa', [\App\Http\Controllers\GuruSiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/{siswa}', [\App\Http\Controllers\GuruSiswaController::class, 'show'])->name('siswa.show');
    Route::get('/kelas/{kelas}', [\App\Http\Controllers\GuruKelasController::class, 'show'])->name('kelas.show');
    Route::get('/penilaian', [\App\Http\Controllers\GuruPenilaianController::class, 'index'])->name('penilaian.index');
    Route::post('/penilaian', [\App\Http\Controllers\GuruPenilaianController::class, 'store'])->name('penilaian.store');
});
Route::prefix('mobile')->group(function () {
    Route::post('/login', [MobileAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [MobileAuthController::class, 'logout']);
        Route::get('/me', [MobileSiswaController::class, 'me']);
        Route::get('/nilai', [MobileSiswaController::class, 'nilai']);
        Route::get('/rekomendasi', [MobileSiswaController::class, 'rekomendasi']);
        Route::get('/rekomendasi/pdf', [MobileSiswaController::class, 'rekomendasiPdf']);
        Route::get('/ping', fn() => response()->json(['ok' => true]));
    });
});
