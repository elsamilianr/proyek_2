<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Varian;
use Illuminate\Http\Request;

class VarianController extends Controller
{
    // FR-14: Daftar semua varian / stok
    public function index(Request $request)
    {
        $query = Varian::with('produk')->latest();

        if ($request->filled('search')) {
            $query->whereHas('produk', fn ($q) =>
                $q->where('nama_produk', 'like', '%' . $request->search . '%')
            )->orWhere('sku', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('stok_filter')) {
            match ($request->stok_filter) {
                'habis'        => $query->where('stok', 0),
                'hampir_habis' => $query->whereBetween('stok', [1, 5]),
                'tersedia'     => $query->where('stok', '>', 5),
                default        => null,
            };
        }

        $varians = $query->paginate(15)->withQueryString();

        return view('karyawan.stok.index', compact('varians'));
    }

    // FR-14.01: Tambah varian pada produk
    public function store(Request $request, Produk $produk)
    {
        $request->validate([
            'warna' => 'nullable|string|max:50',
            'size'  => 'nullable|string|max:20',
            'harga' => 'required|numeric|min:0',
            'stok'  => 'required|integer|min:0',
            'sku'   => 'nullable|string|max:50|unique:varians,sku',
        ]);

        Varian::create([
            'id_produk' => $produk->id,
            'warna'     => $request->warna,
            'size'      => $request->size,
            'harga'     => $request->harga,
            'stok'      => $request->stok,
            'sku'       => $request->sku,
        ]);

        return back()->with('success', 'Varian berhasil ditambahkan.');
    }

    // FR-14.02: Edit varian
    public function update(Request $request, Varian $varian)
    {
        $request->validate([
            'warna' => 'nullable|string|max:50',
            'size'  => 'nullable|string|max:20',
            'harga' => 'required|numeric|min:0',
            'stok'  => 'required|integer|min:0',
            'sku'   => 'nullable|string|max:50|unique:varians,sku,' . $varian->id,
        ]);

        $varian->update($request->only('warna', 'size', 'harga', 'stok', 'sku'));

        return back()->with('success', 'Varian berhasil diperbarui.');
    }

    // FR-14.02: Tambah stok saja (endpoint cepat dari halaman stok)
    public function tambahStok(Request $request, Varian $varian)
    {
        $request->validate(['jumlah' => 'required|integer|min:1']);

        $varian->tambahStok($request->jumlah);

        return back()->with('success', "Stok berhasil ditambah {$request->jumlah} pcs.");
    }

    // FR-14.03: Hapus varian
    public function destroy(Varian $varian)
    {
        $varian->delete();

        return back()->with('success', 'Varian berhasil dihapus.');
    }
}