<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Promo;

class PromoController extends Controller
{
    public function index()
    {
        $promo = Promo::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $promo
        ]);
    }

    public function approve($id)
    {
        $promo = Promo::findOrFail($id);

        $promo->update([
            'status' => 'approved'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Promo disetujui'
        ]);
    }

    public function reject($id)
    {
        $promo = Promo::findOrFail($id);

        $promo->update([
            'status' => 'rejected'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Promo ditolak'
        ]);
    }
}