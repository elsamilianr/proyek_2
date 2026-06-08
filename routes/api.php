<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\StokController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\PromoController;

// ── Public ────────────────────────────────────────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);

// ── Protected (Sanctum) ───────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/transaksi', [TransaksiController::class, 'index']);

    Route::get('/stok',    [StokController::class, 'index']);

    Route::get('/laporan', [LaporanController::class, 'index']);

    // Promo
    Route::get('/promo',                    [PromoController::class, 'index']);
    Route::post('/promo',                   [PromoController::class, 'store']);
    Route::put('/promo/{id}/approve',       [PromoController::class, 'approve']);
    Route::put('/promo/{id}/reject',        [PromoController::class, 'reject']);
});