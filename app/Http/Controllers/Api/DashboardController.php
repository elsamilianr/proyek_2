<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Produk;
use App\Models\Transaksi;

class DashboardController extends Controller
{
    public function index()
    {
        $penjualanHariIni = Transaksi::whereDate(
            'created_at',
            now()
        )->sum('total');

        $produkTerlaris = Produk::orderByDesc('terjual')
            ->take(3)
            ->get();

        return response()->json([
            'success' => true,
            'penjualan_hari_ini' => $penjualanHariIni,
            'produk_terlaris' => $produkTerlaris
        ]);
    }
}