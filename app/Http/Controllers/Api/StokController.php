<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Produk;

class StokController extends Controller
{
    public function index()
    {
        // Load varians agar mobile bisa hitung total_stok & harga_min
        $produk = Produk::with('varians')->aktif()->get()->map(function ($p) {
            return [
                'id'          => $p->id,
                'nama_produk' => $p->nama_produk,
                'kategori'    => $p->kategori,
                'foto'        => $p->foto,
                'is_aktif'    => $p->is_aktif,
                'total_stok'  => $p->total_stok,   // accessor
                'harga_min'   => $p->harga_min,     // accessor
                'terjual'     => $p->varians->sum('terjual') ?? 0,
                'varians'     => $p->varians,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $produk
        ]);
    }
}