<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Transaksi;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $transaksi
        ]);
    }
}