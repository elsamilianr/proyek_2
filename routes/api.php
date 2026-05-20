<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\StokController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\PromoController;

Route::post('/login', [AuthController::class, 'login']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/transaksi', [TransaksiController::class, 'index']);

    Route::get('/stok', [StokController::class, 'index']);

    Route::get('/laporan', [LaporanController::class, 'index']);

    Route::get('/promo', [PromoController::class, 'index']);

    Route::put('/promo/{id}/approve', [PromoController::class, 'approve']);

    Route::put('/promo/{id}/reject', [PromoController::class, 'reject']);