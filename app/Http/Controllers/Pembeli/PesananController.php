<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    // ════════════════════════════════════════════════════════════════════════════
    //  PESANAN
    // ════════════════════════════════════════════════════════════════════════════

    /**
     * Form checkout — tampilkan ringkasan keranjang & pilihan promo.
     */
    public function checkout()
    {
        $keranjang = Keranjang::with(['details.varian.produk'])
            ->where('id_user', Auth::id())
            ->first();

        if (! $keranjang || $keranjang->details->isEmpty()) {
            return redirect()->route('pembeli.keranjang')
                ->with('error', 'Keranjang kosong!');
        }

        $promos    = Promo::where('is_aktif', true)
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->get();

        $cartCount = $keranjang->details->sum('jumlah');

        return view('pembeli.pesanan.checkout', compact('keranjang', 'promos', 'cartCount'));
    }

    /**
     * Buat pesanan baru dari isi keranjang, lalu kosongkan keranjang.
     */
    public function store(Request $request)
    {
        $request->validate(['catatan' => 'nullable|string|max:500']);

        $keranjang = Keranjang::with(['details.varian'])
            ->where('id_user', Auth::id())
            ->firstOrFail();

        if ($keranjang->details->isEmpty()) {
            return redirect()->route('pembeli.keranjang')
                ->with('error', 'Keranjang kosong!');
        }

        $items = $keranjang->details->map(fn($d) => [
            'id_varian' => $d->id_varian,
            'jumlah'    => $d->jumlah,
        ])->toArray();

        $pesanan = Pesanan::checkout(Auth::id(), $items, $request->promo_id ?? null);

        if ($request->filled('catatan')) {
            $pesanan->update(['catatan' => $request->catatan]);
        }

        $keranjang->clearKeranjang();

        return redirect()->route('pembeli.pesanan.pembayaran', $pesanan->id)
            ->with('success', "Pesanan {$pesanan->kode_pesanan} berhasil dibuat!");
    }

    /**
     * Daftar semua pesanan milik user yang login.
     */
    public function index()
    {
        $pesanans  = Pesanan::with(['details.varian.produk', 'pembayaran'])
            ->where('id_user', Auth::id())
            ->latest()
            ->get();

        $cartCount = $this->cartCount();

        return view('pembeli.pesanan.index', compact('pesanans', 'cartCount'));
    }

    /**
     * Detail satu pesanan.
     */
    public function show(int $id)
    {
        $pesanan   = Pesanan::with(['details.varian.produk', 'pembayaran', 'promo'])
            ->where('id_user', Auth::id())
            ->findOrFail($id);

        $cartCount = $this->cartCount();

        return view('pembeli.pesanan.show', compact('pesanan', 'cartCount'));
    }

    // ════════════════════════════════════════════════════════════════════════════
    //  PEMBAYARAN  (bagian dari alur pesanan — tidak dipisah)
    // ════════════════════════════════════════════════════════════════════════════

    /**
     * Form upload bukti pembayaran untuk pesanan tertentu.
     */
    public function formPembayaran(int $pesananId)
    {
        $pesanan   = Pesanan::with(['details.varian.produk', 'pembayaran'])
            ->where('id_user', Auth::id())
            ->findOrFail($pesananId);

        $cartCount = $this->cartCount();

        return view('pembeli.pembayaran.form', compact('pesanan', 'cartCount'));
    }

    /**
     * Simpan bukti pembayaran yang diupload pembeli.
     */
    public function uploadBukti(Request $request, int $pesananId)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'metode'           => 'required|string|in:transfer_bank,qris,cod',
        ]);

        $pesanan = Pesanan::where('id_user', Auth::id())->findOrFail($pesananId);

        $path = $request->file('bukti_pembayaran')
            ->store('bukti_pembayaran', 'public');

        $pembayaran = Pembayaran::firstOrCreate(
            ['id_pesanan' => $pesanan->id],
            [
                'metode'            => $request->metode,
                'status_pembayaran' => 'pending',
                'jumlah_bayar'      => $pesanan->total_harga,
            ]
        );

        $pembayaran->uploadBukti($path);

        return redirect()->route('pembeli.pesanan.show', $pesanan->id)
            ->with('success', 'Bukti pembayaran berhasil dikirim! Menunggu verifikasi.');
    }

    // ── helper ───────────────────────────────────────────────────────────────────
    private function cartCount(): int
    {
        $keranjang = Keranjang::where('id_user', Auth::id())->with('details')->first();
        return $keranjang ? $keranjang->details->sum('jumlah') : 0;
    }
}