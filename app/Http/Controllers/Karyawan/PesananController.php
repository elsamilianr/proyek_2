<?php

namespace App\Http\Controllers\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $query = Pesanan::with(['pembeli', 'pembayaran'])->latest();

        if ($request->filled('status')) {
            $query->where('status_pesanan', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('kode_pesanan', 'like', '%' . $request->search . '%')
                    ->orWhereHas('pembeli', fn ($u) =>
                        $u->where('nama', 'like', '%' . $request->search . '%')
                    );
            });
        }

        $pesanans = $query->paginate(10)->withQueryString();

        return view('karyawan.pesanan.index', compact('pesanans'));
    }

    public function show(Pesanan $pesanan)
    {
        $pesanan->load(['pembeli', 'details.varian.produk', 'pembayaran', 'promo']);
        return view('karyawan.pesanan.show', compact('pesanan'));
    }

    // FR-15.01: Verifikasi / tolak pembayaran
    public function verifikasiPembayaran(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'aksi'    => 'required|in:terima,tolak',
            'catatan' => 'nullable|string|max:255',
        ]);

        $pesanan->load('pembayaran');
        $pembayaran = $pesanan->pembayaran;

        abort_if(! $pembayaran, 404, 'Data pembayaran tidak ditemukan.');
        abort_if(
            ! in_array($pembayaran->status_pembayaran, ['pending', 'menunggu_verifikasi']),
            422,
            'Status pembayaran sudah diproses.'
        );

        // FIX: null-safe operator ?-> agar tidak error jika user null
        $karyawanId = (int) Auth::user()->id;
        $diterima   = $request->aksi === 'terima';

        $pembayaran->konfirmasiPembayaran($karyawanId, $diterima, $request->catatan);

        $pesan = $diterima
            ? 'Pembayaran berhasil diverifikasi. Pesanan sedang diproses.'
            : 'Pembayaran ditolak. Pembeli diminta mengulang pembayaran.';

        return back()->with('success', $pesan);
    }

    // FR-15.02: Ubah status pesanan
    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'status' => 'required|in:diproses,selesai,dibatalkan',
        ]);

        if ($request->status === 'dibatalkan' && $pesanan->status_pesanan !== 'dibatalkan') {
            DB::transaction(function () use ($pesanan, $request) {
                $pesanan->load('details.varian');
                foreach ($pesanan->details as $detail) {
                    if ($detail->varian) {
                        $detail->varian->tambahStok($detail->jumlah);
                    }
                }
                $pesanan->ubahStatus($request->status);
            });
        } else {
            $pesanan->ubahStatus($request->status);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}