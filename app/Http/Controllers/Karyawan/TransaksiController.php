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
    // FR-16: Halaman kasir
    public function create()
    {
        $promos = Promo::aktif()->get();
        return view('karyawan.transaksi.create', compact('promos'));
    }

    // AJAX: Cari varian produk untuk autocomplete kasir
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
                    ->orWhereHas('produk', fn ($p) =>
                        $p->where('nama_produk', 'like', '%' . $keyword . '%')
                    );
            })
            ->take(10)
            ->get()
            ->map(fn ($v) => [
                'id'    => $v->id,
                'label' => $v->produk->nama_produk . ' — ' . $v->label,
                'harga' => (float) $v->harga,
                'stok'  => (int) $v->stok,
                'sku'   => $v->sku ?? '-',
            ]);

        return response()->json($varians);
    }

    // FR-16.01 & FR-16.02: Simpan transaksi
    public function store(Request $request)
    {
        $request->validate([
            'items'             => 'required|array|min:1',
            'items.*.varian_id' => 'required|exists:varians,id',
            'items.*.jumlah'    => 'required|integer|min:1',
            'metode_bayar'      => 'required|in:cash,transfer,qris',
            'promo_id'          => 'nullable|exists:promos,id',
            'uang_diterima'     => 'nullable|numeric|min:0',
            'catatan'           => 'nullable|string|max:255',
        ]);

        try {
            $transaksi = DB::transaction(function () use ($request) {
                $subtotal  = 0;
                $itemsSave = [];

                foreach ($request->items as $item) {
                    $varian = Varian::lockForUpdate()->findOrFail($item['varian_id']);
                    $varian->kurangiStok((int) $item['jumlah']);

                    $sub       = (float) $varian->harga * (int) $item['jumlah'];
                    $subtotal += $sub;

                    $itemsSave[] = [
                        'varian_id'    => $varian->id,
                        'jumlah'       => (int) $item['jumlah'],
                        'harga_satuan' => (float) $varian->harga,
                        'subtotal'     => $sub,
                    ];
                }

                $diskon = 0;
                if ($request->filled('promo_id')) {
                    $promo  = Promo::findOrFail($request->promo_id);
                    $diskon = $promo->hitungDiskon($subtotal);
                }

                $total     = $subtotal - $diskon;
                $kembalian = null;

                if ($request->metode_bayar === 'cash' && $request->filled('uang_diterima')) {
                    $kembalian = (float) $request->uang_diterima - $total;
                }

                // FIX: gunakan auth()->user()->id bukan auth()->id()
                $transaksi = Transaksi::create([
                    'kode_transaksi' => Transaksi::generateKode(),
                    'karyawan_id'    => Auth::user()->id,
                    'promo_id'       => $request->promo_id ?: null,
                    'subtotal'       => $subtotal,
                    'diskon'         => $diskon,
                    'total'          => $total,
                    'metode_bayar'   => $request->metode_bayar,
                    'uang_diterima'  => $request->uang_diterima ?: null,
                    'kembalian'      => $kembalian,
                    'catatan'        => $request->catatan,
                ]);

                $transaksi->details()->createMany($itemsSave);

                return $transaksi;
            });
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()->route('karyawan.transaksi.struk', $transaksi->id)
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    // FR-16.02: Cetak struk
    public function struk(Transaksi $transaksi)
    {
        $transaksi->load(['details.varian.produk', 'karyawan', 'promo']);
        return view('karyawan.transaksi.struk', compact('transaksi'));
    }
}