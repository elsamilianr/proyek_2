<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProdukController extends Controller
{
    /**
     * Tampilkan daftar produk aktif dengan filter.
     */
    public function index(Request $request)
    {
        $query = Produk::with(['varians', 'fotoProduk'])->aktif();

        if ($request->filled('cari')) {
            $query->where('nama_produk', 'like', '%' . $request->cari . '%');
        }

        if ($request->filled('kategori') && $request->kategori !== 'all') {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('harga_min')) {
            $query->whereHas('varians', fn($q) => $q->where('harga', '>=', $request->harga_min));
        }

        if ($request->filled('harga_max')) {
            $query->whereHas('varians', fn($q) => $q->where('harga', '<=', $request->harga_max));
        }

        if ($request->filled('warna')) {
            $query->whereHas('varians', fn($q) => $q->where('warna', $request->warna));
        }

        if ($request->filled('ukuran')) {
            $query->whereHas('varians', fn($q) => $q->where('size', $request->ukuran));
        }

        if ($request->ketersediaan === 'tersedia') {
            $query->whereHas('varians', fn($q) => $q->where('stok', '>', 0));
        } elseif ($request->ketersediaan === 'habis') {
            $query->whereDoesntHave('varians', fn($q) => $q->where('stok', '>', 0));
        }

        $produk      = $query->get();
        $warnaList   = Produk::aktif()
            ->join('varians', 'produks.id', '=', 'varians.id_produk')
            ->distinct()->pluck('varians.warna')->filter()->values();
        $cartCount   = $this->cartCount();

        return view('pembeli.produk.index', compact('produk', 'warnaList', 'cartCount'));
    }

    /**
     * Tampilkan detail satu produk.
     */
    public function show(string $slug)
    {
        $produk    = Produk::with(['varians', 'fotoProduk'])
            ->where('slug', $slug)->aktif()->firstOrFail();
        $cartCount = $this->cartCount();

        return view('pembeli.produk.show', compact('produk', 'cartCount'));
    }

    // ── helper ───────────────────────────────────────────────────────────────────
    private function cartCount(): int
    {
        if (! Auth::check()) return 0;

        $keranjang = Keranjang::where('id_user', Auth::id())->with('details')->first();
        return $keranjang ? $keranjang->details->sum('jumlah') : 0;
    }
}