<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $penjualanHariIni = Transaksi::whereDate('created_at', now())->sum('total');

        // Produk terlaris: hitung dari jumlah di detail_transaksis
        $produkTerlaris = Produk::with('varians')
            ->withSum('varians as total_terjual', 'stok') // fallback
            ->select('produks.*')
            ->selectSub(
                DB::table('detail_transaksis')
                    ->join('varians', 'varians.id', '=', 'detail_transaksis.varian_id')
                    ->whereColumn('varians.id_produk', 'produks.id')
                    ->selectRaw('COALESCE(SUM(detail_transaksis.jumlah), 0)'),
                'terjual'
            )
            ->whereNull('produks.deleted_at')
            ->orderByDesc('terjual')
            ->take(3)
            ->get()
            ->map(function ($p) {
                return [
                    'id'          => $p->id,
                    'nama_produk' => $p->nama_produk,
                    'kategori'    => $p->kategori,
                    'foto'        => $p->foto,
                    'total_stok'  => $p->total_stok,
                    'harga_min'   => $p->harga_min,
                    'terjual'     => (int) $p->terjual,
                ];
            });

        return response()->json([
            'success'            => true,
            'penjualan_hari_ini' => $penjualanHariIni,
            'produk_terlaris'    => $produkTerlaris,
        ]);
    }
}