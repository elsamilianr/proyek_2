<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Transaksi;

class TransaksiController extends Controller
{
    public function index()
    {
        // Load karyawan (user) agar mobile bisa tampilkan nama kasir
        $transaksi = Transaksi::with([
            'karyawan:id,nama',
            'details.varian.produk:id,nama_produk',
        ])->latest()->get();

        return response()->json([
            'success' => true,
            'data'    => $transaksi
        ]);
    }
}