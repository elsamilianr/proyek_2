<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    // FR-17: Riwayat seluruh transaksi online & offline
    public function index(Request $request)
    {
        $tipe = $request->input('tipe', 'semua');

        // ── Pesanan online (sudah selesai) ────────────────────────────────────────
        $queryOnline = Pesanan::with(['pembeli', 'pembayaran'])
            ->where('status_pesanan', 'selesai');

        // ── Transaksi offline (kasir) ─────────────────────────────────────────────
        $queryOffline = Transaksi::with('karyawan');

        // ── Filter tanggal ────────────────────────────────────────────────────────
        if ($request->filled('dari')) {
            $queryOnline->whereDate('created_at', '>=', $request->dari);
            $queryOffline->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $queryOnline->whereDate('created_at', '<=', $request->sampai);
            $queryOffline->whereDate('created_at', '<=', $request->sampai);
        }

        $pesanans = in_array($tipe, ['semua', 'online'])
            ? $queryOnline->latest()->paginate(10, ['*'], 'online')->withQueryString()
            : collect();

        $transaksis = in_array($tipe, ['semua', 'offline'])
            ? $queryOffline->latest()->paginate(10, ['*'], 'offline')->withQueryString()
            : collect();

        return view('karyawan.riwayat.index', compact('pesanans', 'transaksis', 'tipe'));
    }

    public function showPesanan(Pesanan $pesanan)
    {
        $pesanan->load(['pembeli', 'details.varian.produk', 'pembayaran', 'promo']);
        return view('karyawan.riwayat.show-pesanan', compact('pesanan'));
    }

    public function showTransaksi(Transaksi $transaksi)
    {
        $transaksi->load(['karyawan', 'details.varian.produk', 'promo']);
        return view('karyawan.riwayat.show-transaksi', compact('transaksi'));
    }
}
