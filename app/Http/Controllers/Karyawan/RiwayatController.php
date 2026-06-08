<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $tipe = $request->input('tipe', 'semua');

        // Pesanan online yang sudah final
        $queryOnline = Pesanan::withTrashed()
            ->with(['pembeli', 'pembayaran', 'details.varian.produk'])
            ->whereIn('status_pesanan', ['selesai', 'dibatalkan']);

        // Transaksi offline
        $queryOffline = Transaksi::with([
            'karyawan',
            'details.varian.produk'
        ]);

        // Filter tanggal
        if ($request->filled('dari')) {
            $queryOnline->whereDate('created_at', '>=', $request->dari);
            $queryOffline->whereDate('created_at', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $queryOnline->whereDate('created_at', '<=', $request->sampai);
            $queryOffline->whereDate('created_at', '<=', $request->sampai);
        }

        $pesanans = in_array($tipe, ['semua', 'online'])
            ? $queryOnline->latest()
                ->paginate(10, ['*'], 'online')
                ->withQueryString()
            : collect();

        $transaksis = in_array($tipe, ['semua', 'offline'])
            ? $queryOffline->latest()
                ->paginate(10, ['*'], 'offline')
                ->withQueryString()
            : collect();

        return view('karyawan.riwayat.index', compact(
            'pesanans',
            'transaksis',
            'tipe'
        ));
    }

    public function showPesanan($id)
    {
        $pesanan = Pesanan::withTrashed()
            ->with([
                'pembeli', 
                'details.varian.produk',
                'pembayaran',
                'promo'
            ])
            ->findOrFail($id);

        return view('karyawan.riwayat.show-transaksi', compact('pesanan'));
    }

    public function showTransaksi($id)
    {
        $transaksi = Transaksi::with([
            'karyawan',
            'details.varian.produk',
            'promo'
        ])->findOrFail($id);

        return view('karyawan.riwayat.show-transaksi-offline', compact('transaksi'));
    }
}