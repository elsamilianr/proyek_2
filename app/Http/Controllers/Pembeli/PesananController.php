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
    // ─────────────────────────────────────────────────────────────────────────────
    // Halaman checkout — hanya tampilkan form, belum buat pesanan
    // ─────────────────────────────────────────────────────────────────────────────
    public function checkout()
    {
        $keranjang = Keranjang::with(['details.varian.produk'])
            ->where('id_user', Auth::id())
            ->first();

        if (!$keranjang || $keranjang->details->isEmpty()) {
            return redirect()->route('pembeli.keranjang')
                ->with('error', 'Keranjang kosong!');
        }

        $promos    = Promo::aktif()->get();
        $cartCount = $keranjang->details->sum('jumlah');

        return view('pembeli.pesanan.checkout', compact('keranjang', 'promos', 'cartCount'));
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // store() — simpan data checkout ke SESSION, belum buat pesanan di DB
    // Pesanan hanya dibuat setelah:
    //   - Cash       → langsung di sini
    //   - Transfer   → setelah upload bukti (uploadBukti)
    //   - Midtrans   → setelah Snap sukses (konfirmasiMidtrans)
    // ─────────────────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500',
            'metode'  => 'required|in:transfer,cash,midtrans',
        ]);

        $keranjang = Keranjang::with(['details.varian'])
            ->where('id_user', Auth::id())
            ->firstOrFail();

        if ($keranjang->details->isEmpty()) {
            return redirect()->route('pembeli.keranjang')
                ->with('error', 'Keranjang kosong!');
        }

        // ── CASH: buat pesanan langsung ──────────────────────────────────────────
        if ($request->metode === 'cash') {
            $pesanan = $this->buatPesananDariKeranjang($keranjang, $request->promo_id, $request->catatan);
            $pesanan->update(['status_pesanan' => 'diproses']);

            return redirect()
                ->route('pembeli.pesanan.show', $pesanan->id)
                ->with('success', 'Pesanan berhasil dibuat. Silakan ambil dan bayar langsung di toko.');
        }

        // ── TRANSFER & MIDTRANS: simpan ke session dulu ──────────────────────────
        $items = $keranjang->details->map(fn($d) => [
            'id_varian' => $d->id_varian,
            'jumlah'    => $d->jumlah,
        ])->toArray();

        session([
            'checkout_data' => [
                'items'    => $items,
                'promo_id' => $request->promo_id ?? null,
                'catatan'  => $request->catatan,
                'metode'   => $request->metode,
            ],
        ]);

        // ── MIDTRANS: kembalikan JSON agar JS bisa lanjut ke Snap ────────────────
        if ($request->metode === 'midtrans') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['status' => 'pending_snap']);
            }
        }

        // ── TRANSFER: redirect ke form upload bukti ──────────────────────────────
        return redirect()->route('pembeli.pesanan.pembayaran.form')
            ->with('success', 'Silakan upload bukti pembayaran untuk menyelesaikan pesanan.');
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // konfirmasiMidtrans() — dipanggil JS setelah Snap sukses/pending
    // Baru di sini pesanan dibuat di DB
    // ─────────────────────────────────────────────────────────────────────────────
    public function konfirmasiMidtrans(Request $request)
    {
        $data = session('checkout_data');

        if (!$data || $data['metode'] !== 'midtrans') {
            return response()->json(['error' => 'Data checkout tidak ditemukan. Silakan ulangi dari keranjang.'], 422);
        }

        $keranjang = Keranjang::with(['details.varian'])
            ->where('id_user', Auth::id())
            ->firstOrFail();

        // Buat pesanan di DB
        $pesanan = Pesanan::checkout(Auth::id(), $data['items'], $data['promo_id'] ?? null);

        if (!empty($data['catatan'])) {
            $pesanan->update(['catatan' => $data['catatan']]);
        }

        // Ambil status dari request (sukses/pending)
        $statusBayar = $request->input('status', 'pending');

        // Ambil channel yang dipilih user dari request (qris, gopay, credit_card, dll)
        // Fallback ke 'midtrans' jika tidak ada
        $channelDipilih = $request->input('midtrans_channel', 'midtrans');

        // Buat record pembayaran
        Pembayaran::create([
            'id_pesanan'        => $pesanan->id,
            'metode'            => $channelDipilih,
            'status_pembayaran' => 'menunggu_verifikasi',
            'jumlah_bayar'      => $pesanan->total_harga,
            'tanggal_bayar'     => now(),
        ]);

        // Ubah status pesanan ke menunggu_verifikasi
        $pesanan->ubahStatus('menunggu_verifikasi');

        // Kosongkan keranjang & hapus session
        $keranjang->clearKeranjang();
        session()->forget(['checkout_data', 'checkout_midtrans_temp_id', 'checkout_midtrans_channel']);

        return response()->json(['pesanan_id' => $pesanan->id]);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // formPembayaran() — halaman upload bukti untuk Transfer Bank
    // ─────────────────────────────────────────────────────────────────────────────
    public function formPembayaran()
    {
        $data = session('checkout_data');

        if (!$data || $data['metode'] !== 'transfer') {
            return redirect()->route('pembeli.keranjang')
                ->with('error', 'Sesi checkout tidak ditemukan. Silakan ulangi.');
        }

        // Hitung total sementara untuk ditampilkan
        $keranjang = Keranjang::with(['details.varian.produk'])
            ->where('id_user', Auth::id())
            ->first();

        $cartCount = $this->cartCount();

        return view('pembeli.pembayaran.form', compact('keranjang', 'cartCount', 'data'));
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // uploadBukti() — upload bukti Transfer Bank, baru buat pesanan di DB
    // ─────────────────────────────────────────────────────────────────────────────
    public function uploadBukti(Request $request)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $data = session('checkout_data');

        if (!$data || $data['metode'] !== 'transfer') {
            return redirect()->route('pembeli.keranjang')
                ->with('error', 'Sesi checkout tidak ditemukan. Silakan ulangi.');
        }

        $keranjang = Keranjang::with(['details.varian'])
            ->where('id_user', Auth::id())
            ->firstOrFail();

        // Buat pesanan di DB
        $pesanan = Pesanan::checkout(Auth::id(), $data['items'], $data['promo_id'] ?? null);

        if (!empty($data['catatan'])) {
            $pesanan->update(['catatan' => $data['catatan']]);
        }

        // Upload bukti
        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');

        $pembayaran = Pembayaran::create([
            'id_pesanan'        => $pesanan->id,
            'metode'            => 'transfer',
            'status_pembayaran' => 'pending',
            'jumlah_bayar'      => $pesanan->total_harga,
        ]);
        $pembayaran->uploadBukti($path);

        // Kosongkan keranjang & hapus session
        $keranjang->clearKeranjang();
        session()->forget('checkout_data');

        return redirect()
            ->route('pembeli.pesanan.show', $pesanan->id)
            ->with('success', 'Bukti pembayaran berhasil dikirim! Pesanan sedang diproses.');
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // uploadBuktiPesanan() — upload/perbarui bukti di halaman detail pesanan
    // (pesanan sudah ada di DB, hanya update bukti)
    // ─────────────────────────────────────────────────────────────────────────────
    public function uploadBuktiPesanan(Request $request, int $pesananId)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $pesanan = Pesanan::with('pembayaran')->where('id_user', Auth::id())->findOrFail($pesananId);

        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');

        // Tentukan metode: gunakan metode asli dari pembayaran jika sudah ada,
        // atau ambil dari field 'metode' di request (dikirim dari form hidden)
        $metodeLama = $pesanan->pembayaran?->metode ?? $request->input('metode', 'transfer');

        // Ambil atau buat record pembayaran
        $pembayaran = $pesanan->pembayaran ?? Pembayaran::create([
            'id_pesanan'        => $pesanan->id,
            'metode'            => $metodeLama,
            'status_pembayaran' => 'pending',
            'jumlah_bayar'      => $pesanan->total_harga,
        ]);

        // Update bukti, status terverifikasi, dan pastikan metode tersimpan benar
        $pembayaran->update([
            'bukti_pembayaran'  => $path,
            'status_pembayaran' => 'terverifikasi',
            'metode'            => $metodeLama,
            'tanggal_bayar'     => now(),
        ]);

        // Update status pesanan langsung ke diproses
        $pesanan->ubahStatus('diproses');

        return redirect()
            ->route('pembeli.pesanan.show', $pesanan->id)
            ->with('success', 'Bukti pembayaran berhasil dikirim! Pesanan sedang diproses.');
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────────
    public function index()
    {
        $pesanans = Pesanan::with(['details.varian.produk', 'pembayaran'])
            ->where('id_user', Auth::id())
            ->latest()
            ->get();

        $cartCount = $this->cartCount();

        return view('pembeli.pesanan.index', compact('pesanans', 'cartCount'));
    }

    public function show(int $id)
    {
        $pesanan = Pesanan::with(['details.varian.produk', 'pembayaran', 'promo'])
            ->where('id_user', Auth::id())
            ->findOrFail($id);

        $cartCount = $this->cartCount();

        return view('pembeli.pesanan.show', compact('pesanan', 'cartCount'));
    }

    private function buatPesananDariKeranjang($keranjang, $promoId, $catatan): Pesanan
    {
        $items = $keranjang->details->map(fn($d) => [
            'id_varian' => $d->id_varian,
            'jumlah'    => $d->jumlah,
        ])->toArray();

        $pesanan = Pesanan::checkout(Auth::id(), $items, $promoId);

        if ($catatan) {
            $pesanan->update(['catatan' => $catatan]);
        }

        $keranjang->clearKeranjang();

        return $pesanan;
    }

    private function cartCount(): int
    {
        $keranjang = Keranjang::where('id_user', Auth::id())
            ->with('details')->first();

        return $keranjang ? $keranjang->details->sum('jumlah') : 0;
    }
}