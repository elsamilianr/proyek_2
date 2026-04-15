<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\FotoProduk;
use App\Models\Produk;
use App\Models\Varian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProdukController extends Controller
{
    // FR-13: Daftar produk
    public function index(Request $request)
    {
        $query = Produk::with('varians')->latest();

        if ($request->filled('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('is_aktif', $request->status === 'aktif');
        }

        $produks   = $query->paginate(10)->withQueryString();
        $kategoris = Produk::select('kategori')->distinct()->pluck('kategori');

        return view('karyawan.produk.index', compact('produks', 'kategoris'));
    }

    public function show(Produk $produk)
    {
        $produk->load(['varians', 'fotoProduk']);
        return view('karyawan.produk.show', compact('produk'));
    }

    // FR-13.01: Tambah produk
    public function create()
    {
        return view('karyawan.produk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk'       => 'required|string|max:255',
            'deskripsi'         => 'nullable|string',
            'kategori'          => 'nullable|string|max:100',
            'foto'              => 'nullable|image|max:2048',
            'foto_tambahan.*'   => 'nullable|image|max:2048',
            'varians'           => 'required|array|min:1',
            'varians.*.warna'   => 'nullable|string|max:50',
            'varians.*.size'    => 'nullable|string|max:20',
            'varians.*.harga'   => 'required|numeric|min:0',
            'varians.*.stok'    => 'required|integer|min:0',
            'varians.*.sku'     => 'nullable|string|max:50|distinct',
        ]);

        DB::transaction(function () use ($request) {
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('produk', 'public');
            }

            $produk = Produk::create([
                'nama_produk' => $request->nama_produk,
                'slug'        => Str::slug($request->nama_produk) . '-' . Str::random(5),
                'deskripsi'   => $request->deskripsi,
                'kategori'    => $request->kategori,
                'foto'        => $fotoPath,
                'is_aktif'    => true,
            ]);

            // Foto tambahan
            if ($request->hasFile('foto_tambahan')) {
                foreach ($request->file('foto_tambahan') as $foto) {
                    FotoProduk::create([
                        'produk_id' => $produk->id,
                        'foto'      => $foto->store('produk', 'public'),
                    ]);
                }
            }

            // Simpan varian
            foreach ($request->varians as $vd) {
                Varian::create([
                    'id_produk' => $produk->id,
                    'warna'     => $vd['warna']  ?? null,
                    'size'      => $vd['size']   ?? null,
                    'harga'     => $vd['harga'],
                    'stok'      => $vd['stok'],
                    'sku'       => $vd['sku']    ?? null,
                ]);
            }
        });

        return redirect()->route('karyawan.produk.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    // FR-13.02: Edit produk
    public function edit(Produk $produk)
    {
        $produk->load(['varians', 'fotoProduk']);
        return view('karyawan.produk.edit', compact('produk'));
    }

    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'kategori'    => 'nullable|string|max:100',
            'foto'        => 'nullable|image|max:2048',
            'is_aktif'    => 'nullable|boolean',
        ]);

        if ($request->hasFile('foto')) {
            if ($produk->foto) Storage::disk('public')->delete($produk->foto);
            $produk->foto = $request->file('foto')->store('produk', 'public');
        }

        $produk->update([
            'nama_produk' => $request->nama_produk,
            'deskripsi'   => $request->deskripsi,
            'kategori'    => $request->kategori,
            'is_aktif'    => $request->boolean('is_aktif', true),
        ]);

        return redirect()->route('karyawan.produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    // FR-13.03: Hapus produk
    public function destroy(Produk $produk)
    {
        $produk->delete();

        return redirect()->route('karyawan.produk.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
