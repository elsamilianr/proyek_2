<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        $laporan = DB::table('transaksis')
            ->select(
                'customer',
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(id) as transaksi')
            )
            ->groupBy('customer')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $laporan
        ]);
    }
}