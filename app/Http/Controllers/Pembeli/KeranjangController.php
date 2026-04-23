<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeranjangController extends Controller
{
    /**
     * Tampilkan isi keranjang milik user yang login.
     */
    public function index()
    {
        $keranjang = Keranjang::with(['details.varian.produk'])
            ->where('id_user', Auth::id())
            ->first();

        $cartCount = $keranjang ? $keranjang->details->sum('jumlah') : 0;

        return view('pembeli.keranjang.index', compact('keranjang', 'cartCount'));
    }

    /**
     * Tambah item ke keranjang.
     */
    public function tambah(Request $request)
    {
        $request->validate([
            'varian_id' => 'required|exists:varians,id',
            'jumlah'    => 'required|integer|min:1',
        ]);

        $keranjang = Keranjang::firstOrCreate(['id_user' => Auth::id()]);
        $keranjang->tambahItem($request->varian_id, $request->jumlah);

        return back()->with('success', 'Produk ditambahkan ke keranjang!');
    }

    /**
     * Update jumlah item di keranjang.
     */
    public function update(Request $request, int $detailId)
    {
        $request->validate(['jumlah' => 'required|integer|min:1']);

        $keranjang = Keranjang::where('id_user', Auth::id())->firstOrFail();
        $keranjang->ubahJumlah($detailId, $request->jumlah);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    /**
     * Hapus satu item dari keranjang.
     */
    public function hapus(int $detailId)
    {
        $keranjang = Keranjang::where('id_user', Auth::id())->firstOrFail();
        $keranjang->hapusItem($detailId);

        return back()->with('success', 'Item dihapus dari keranjang.');
    }
}