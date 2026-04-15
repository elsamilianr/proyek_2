<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\Varian;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Kartu ringkasan ──────────────────────────────────────────────────────
        $totalProduk     = Produk::count();
        $stokHampirHabis = Varian::where('stok', '<=', 5)->where('stok', '>', 0)->count();
        $stokHabis       = Varian::where('stok', 0)->count();
        $pesananMenunggu = Pesanan::whereIn('status_pesanan', [
            'menunggu_pembayaran', 'menunggu_verifikasi', 'diproses',
        ])->count();

        // ── Omzet bulan ini ──────────────────────────────────────────────────────
        $omzetOnline = Pesanan::where('status_pesanan', 'selesai')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_harga');

        $omzetOffline = Transaksi::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $omzetBulanIni = $omzetOnline + $omzetOffline;

        // ── Pesanan terbaru (5) ───────────────────────────────────────────────────
        $pesananTerbaru = Pesanan::with(['pembeli', 'pembayaran'])
            ->latest()
            ->take(5)
            ->get();

        // ── Produk terlaris (5) berdasarkan transaksi offline ─────────────────────
        $produkTerlaris = DB::table('detail_transaksis as dt')
            ->join('varians as v', 'v.id', '=', 'dt.varian_id')
            ->join('produks as p', 'p.id', '=', 'v.id_produk')
            ->selectRaw('p.id, p.nama_produk, p.foto, SUM(dt.jumlah) as total_terjual')
            ->groupBy('p.id', 'p.nama_produk', 'p.foto')
            ->orderByDesc('total_terjual')
            ->take(5)
            ->get();

        return view('karyawan.dashboard', compact(
            'totalProduk',
            'stokHampirHabis',
            'stokHabis',
            'pesananMenunggu',
            'omzetBulanIni',
            'pesananTerbaru',
            'produkTerlaris'
        ));
    }
}
