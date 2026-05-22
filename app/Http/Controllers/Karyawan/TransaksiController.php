<?php

namespace App\Http\Controllers\Karyawan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Promo;
use App\Models\Transaksi;
use App\Models\Varian;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function create()
    {
        $promos = Promo::aktif()->get();

        $varians = Varian::with('produk')
            ->where('stok', '>', 0)
            ->get();

        return view('karyawan.transaksi.create', compact('promos', 'varians'));
    }

    public function cariVarian(Request $request)
    {
        $keyword = $request->input('q', '');

        if (strlen($keyword) < 1) {
            return response()->json([]);
        }

        $varians = Varian::with('produk')
            ->where('stok', '>', 0)
            ->where(function ($q) use ($keyword) {
                $q->where('sku', 'like', '%' . $keyword . '%')
                ->orWhereHas('produk', function ($p) use ($keyword) {
                    $p->where('nama_produk', 'like', '%' . $keyword . '%');
                });
            })
            ->take(10)
            ->get();

        return response()->json($varians)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    public function store(Request $request)
    {
        $request->validate([
            'items'             => 'required|array|min:1',
            'items.*.varian_id' => 'required|exists:varians,id',
            'items.*.jumlah'    => 'required|integer|min:1',
            'metode_bayar'      => 'required|in:cash,transfer,qris',
        ]);

        try {
            $transaksi = DB::transaction(function () use ($request) {

                $subtotal = 0;
                $itemsSave = [];

                foreach ($request->items as $item) {
                    $varian = Varian::lockForUpdate()->findOrFail($item['varian_id']);

                    $varian->kurangiStok((int)$item['jumlah']);

                    $sub = $varian->harga * $item['jumlah'];
                    $subtotal += $sub;

                    $itemsSave[] = [
                        'varian_id'    => $varian->id,
                        'jumlah'       => $item['jumlah'],
                        'harga_satuan' => $varian->harga,
                        'subtotal'     => $sub,
                    ];
                }

                $transaksi = Transaksi::create([
                    'kode_transaksi' => Transaksi::generateKode(),
                    'karyawan_id'    => Auth::user()->id,
                    'subtotal'       => $subtotal,
                    'total'          => $subtotal,
                    'metode_bayar'   => $request->metode_bayar,
                ]);

                $transaksi->details()->createMany($itemsSave);

                return $transaksi;
            });

            return redirect()
                ->route('karyawan.transaksi.struk', $transaksi->id)
                ->with('success', 'Transaksi berhasil');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function struk(Transaksi $transaksi)
    {
        $transaksi->load(['details.varian.produk', 'karyawan']);
        return view('karyawan.transaksi.struk', compact('transaksi'));
    }
}