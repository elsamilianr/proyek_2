<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Transaksi;
use App\Models\Varian;

class DashboardController extends Controller
{
    public function index()
    {
        // Kartu ringkasan
        $penjualanHariIni = Transaksi::whereDate('created_at', today())->count()
            + Pesanan::whereDate('created_at', today())->count();

        $totalTransaksiOnline  = Pesanan::where('status_pesanan', 'selesai')
            ->whereDate('created_at', today())->sum('total_harga');
        $totalTransaksiOffline = Transaksi::whereDate('created_at', today())->sum('total');
        $totalTransaksi        = $totalTransaksiOnline + $totalTransaksiOffline;

        $stokMenipis = Varian::where('stok', '<=', 5)->where('stok', '>', 0)->count();

        // Pesanan masuk (belum selesai/batal)
        $pesananMasuk = Pesanan::whereIn('status_pesanan', [
                'menunggu_pembayaran', 'menunggu_verifikasi', 'diproses',
            ])->latest()->take(5)->get();

        // Stok menipis detail
        $stokMenipisData = Varian::with('produk')
            ->where('stok', '<=', 5)->where('stok', '>', 0)
            ->orderBy('stok')->take(5)->get();

        // Transaksi offline hari ini
        $transaksiHariIni = Transaksi::whereDate('created_at', today())->latest()->get();

        return view('karyawan.dashboard', compact(
            'penjualanHariIni',
            'totalTransaksi',
            'stokMenipis',
            'pesananMasuk',
            'stokMenipisData',
            'transaksiHariIni',
        ));
    }
}