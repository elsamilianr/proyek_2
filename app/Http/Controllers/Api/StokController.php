<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Produk;

class StokController extends Controller
{
    public function index()
    {
        $produk = Produk::all();

        return response()->json([
            'success' => true,
            'data' => $produk
        ]);
    }
}