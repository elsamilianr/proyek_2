<?php

namespace App\Http\Controllers\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Promo;
use Illuminate\Support\Facades\Storage;

class PromoController extends Controller
{
    public function index(Request $request)
    {
        $query = Promo::with('pengaju')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('nama_promo', 'like', '%' . $request->search . '%');
        }

        $promos = $query->paginate(10)->withQueryString();

        return view('karyawan.promo.index', compact('promos'));
    }

    public function show(Promo $promo)
    {
        $promo->load(['pengaju', 'penyetuju', 'produks']);
        return view('karyawan.promo.show', compact('promo'));
    }

    public function create()
    {
        $produks = Produk::aktif()->get();
        return view('karyawan.promo.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_promo'      => 'required|string|max:255',
            'deskripsi'       => 'nullable|string',
            'tipe_diskon'     => 'required|in:persen,nominal',
            'diskon'          => 'required|numeric|min:0',
            'min_pembelian'   => 'nullable|numeric|min:0',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'foto'            => 'nullable|image|max:2048',
            'produk_ids'      => 'nullable|array',
            'produk_ids.*'    => 'exists:produks,id',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('promo', 'public');
        }

        // FIX: gunakan auth()->user()->id bukan auth()->id()
        $promo = Promo::create([
            'diajukan_oleh'   => Auth::user()->id,
            'nama_promo'      => $request->nama_promo,
            'deskripsi'       => $request->deskripsi,
            'tipe_diskon'     => $request->tipe_diskon,
            'diskon'          => $request->diskon,
            'min_pembelian'   => $request->min_pembelian ?? 0,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'foto'            => $fotoPath,
            'status'          => 'pending',
        ]);

        if ($request->filled('produk_ids')) {
            $promo->produks()->attach($request->produk_ids);
        }

        return redirect()->route('karyawan.promo.index')
            ->with('success', 'Promo berhasil diajukan dan menunggu persetujuan pemilik.');
    }

    public function destroy(Promo $promo)
    {
        // FIX: gunakan auth()->user()->id dan cast keduanya ke int
        // agar perbandingan tidak gagal karena beda tipe (string vs int)
        $userId      = (int) Auth::user()->id;
        $pengajuId   = (int) $promo->diajukan_oleh;
        $milikSendiri = $userId === $pengajuId;
        $bolehHapus   = $milikSendiri && $promo->status !== 'aktif';

        abort_if(! $bolehHapus, 403, 'Anda tidak dapat menghapus promo ini.');

        if ($promo->foto) {
            Storage::disk('public')->delete($promo->foto);
        }

        $promo->delete();

        return redirect()->route('karyawan.promo.index')
            ->with('success', 'Promo berhasil dihapus.');
    }
}