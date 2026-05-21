<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Varian;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function index()
    {
        $produks = Produk::with('varians')
            ->latest()
            ->get();

        return view('karyawan.produk.index', compact('produks'));
    }

    public function create()
    {
        return view('karyawan.produk.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk'          => 'required|string|max:255',
            'kategori'             => 'nullable|string|max:255',
            'deskripsi'            => 'nullable|string',
            'foto'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'varians'              => 'required|array|min:1',
            'varians.*.harga'      => 'required|numeric|min:0',
            'varians.*.stok'       => 'required|integer|min:0',
            'varians.*.warna'      => 'nullable|string|max:50',
            'varians.*.size'       => 'nullable|string|max:20',
            'varians.*.sku'        => 'nullable|string|max:50|distinct',
        ]);

        $fotoPath = null;

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('produk', 'public');
        }

        $produk = Produk::create([
            'nama_produk' => $validated['nama_produk'],
            'kategori'    => $validated['kategori'] ?? null,
            'deskripsi'   => $validated['deskripsi'] ?? null,
            'foto'        => $fotoPath,
            'is_aktif'    => true,
        ]);

        // Simpan semua varian yang dikirim dari form create
        foreach ($validated['varians'] as $varianData) {
            $produk->varians()->create([
                'warna' => $varianData['warna'] ?? null,
                'size'  => $varianData['size']  ?? null,
                'harga' => $varianData['harga'],
                'stok'  => $varianData['stok'],
                'sku'   => $varianData['sku']   ?? null,
            ]);
        }

        return redirect()
            ->route('karyawan.produk.edit', $produk->id)
            ->with('success', 'Produk dan varian berhasil ditambahkan.');
    }

    public function edit(Produk $produk)
    {
        $produk->load('varians');

        return view('karyawan.produk.edit', compact('produk'));
    }

    public function update(Request $request, Produk $produk)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori'    => 'nullable|string|max:255',
            'deskripsi'   => 'nullable|string',
            'foto'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // update produk
        $produk->update([
            'nama_produk' => $validated['nama_produk'],
            'kategori'    => $validated['kategori'] ?? null,
            'deskripsi'   => $validated['deskripsi'] ?? null,
        ]);

        // update foto
        if ($request->hasFile('foto')) {

            if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
                Storage::disk('public')->delete($produk->foto);
            }

            $fotoPath = $request->file('foto')->store('produk', 'public');

            $produk->update([
                'foto' => $fotoPath
            ]);
        }

        // update semua varian
        if ($request->has('varians')) {

            foreach ($request->varians as $id => $varianData) {

                $varian = Varian::find($id);

                if ($varian) {

                    $varian->update([
                        'warna' => $varianData['warna'] ?? null,
                        'size'  => $varianData['size'] ?? null,
                        'harga' => $varianData['harga'],
                        'stok'  => $varianData['stok'],
                    ]);
                }
            }
        }

        return redirect()
            ->route('karyawan.produk.edit', $produk->id)
            ->with('success', 'Produk dan semua varian berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        // hapus foto
        if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
            Storage::disk('public')->delete($produk->foto);
        }

        // hapus semua varian
        $produk->varians()->delete();

        // hapus produk
        $produk->delete();

        return redirect()
            ->route('karyawan.produk.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}