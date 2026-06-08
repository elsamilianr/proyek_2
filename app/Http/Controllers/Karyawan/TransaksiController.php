<?php

namespace App\Http\Controllers\Karyawan;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Promo;
use App\Models\Transaksi;
use App\Models\Varian;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class TransaksiController extends Controller
{
    const MIDTRANS_METHODS = ['qris', 'gopay', 'credit_card'];

    const MIDTRANS_CHANNEL_MAP = [
        'qris'        => ['other_qris'],
        'gopay'       => ['gopay'],
        'credit_card' => ['credit_card'],
    ];

    public function __construct()
    {
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized  = config('services.midtrans.is_sanitized');
        Config::$is3ds        = config('services.midtrans.is_3ds');
        Config::$curlOptions  = [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CAINFO         => false,
            CURLOPT_HTTPHEADER     => [],
        ];
    }

    public function create()
    {
        $promos = Promo::aktif()->get();

        $varians = Varian::with('produk')
            ->where('stok', '>', 0)
            ->get();

        return view('karyawan.transaksi.create', compact('promos', 'varians'));
    }

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
                ->orWhereHas('produk', function ($p) use ($keyword) {
                    $p->where('nama_produk', 'like', '%' . $keyword . '%');
                });
            })
            ->take(10)
            ->get();

        return response()->json($varians)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    /**
     * Buat Snap Token Midtrans untuk transaksi offline karyawan.
     */
    public function getMidtransToken(Request $request): JsonResponse
    {
        $request->validate([
            'items'             => 'required|array|min:1',
            'items.*.varian_id' => 'required|exists:varians,id',
            'items.*.jumlah'    => 'required|integer|min:1',
            'metode_bayar'      => 'required|in:qris,gopay,credit_card',
        ]);

        $user   = Auth::user();
        $tempId = 'OFFLINE-' . $user->id . '-' . time();

        $subtotal    = 0;
        $itemDetails = [];

        foreach ($request->items as $item) {
            $varian = Varian::with('produk')->findOrFail($item['varian_id']);

            $itemTotal = $varian->harga * $item['jumlah'];
            $subtotal += $itemTotal;

            $itemDetails[] = [
                'id'       => $varian->id,
                'price'    => (int) $varian->harga,
                'quantity' => (int) $item['jumlah'],
                'name'     => substr($varian->produk->nama_produk ?? 'Produk', 0, 50),
            ];
        }

        $diskon = 0;
        if (!empty($request->promo_id)) {
            $promo  = Promo::find($request->promo_id);
            $diskon = $promo ? $promo->hitungDiskon($subtotal) : 0;
            if ($diskon > 0) {
                $itemDetails[] = [
                    'id'       => 'DISKON',
                    'price'    => -(int) $diskon,
                    'quantity' => 1,
                    'name'     => 'Diskon Promo',
                ];
            }
        }

        $total = $subtotal - $diskon;

        $enabledPayments = self::MIDTRANS_CHANNEL_MAP[$request->metode_bayar] ?? null;

        $params = [
            'transaction_details' => [
                'order_id'     => $tempId,
                'gross_amount' => (int) $total,
            ],
            'customer_details' => [
                'first_name' => $user->nama ?? $user->name,
                'email'      => $user->email,
                'phone'      => $user->no_hp ?? '',
            ],
            'item_details' => $itemDetails,
        ];

        if ($enabledPayments) {
            $params['enabled_payments'] = $enabledPayments;
        }

        try {
            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'snap_token'    => $snapToken,
                'client_key'    => config('services.midtrans.client_key'),
                'temp_order_id' => $tempId,
                'total'         => $total,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans offline token error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat token pembayaran: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'items'             => 'required|array|min:1',
            'items.*.varian_id' => 'required|exists:varians,id',
            'items.*.jumlah'    => 'required|integer|min:1',
            'metode_bayar'      => 'required|in:cash,transfer,qris,gopay,credit_card',
        ]);

        try {
            $transaksi = DB::transaction(function () use ($request) {

                $subtotal  = 0;
                $itemsSave = [];

                foreach ($request->items as $item) {
                    $varian = Varian::lockForUpdate()->findOrFail($item['varian_id']);

                    $varian->kurangiStok((int) $item['jumlah']);

                    $sub = $varian->harga * $item['jumlah'];
                    $subtotal += $sub;

                    $itemsSave[] = [
                        'varian_id'    => $varian->id,
                        'jumlah'       => (int) $item['jumlah'],
                        'harga_satuan' => $varian->harga,
                        'subtotal'     => $sub,
                    ];
                }

                $diskon  = 0;
                $promoId = $request->promo_id ?? null;
                if ($promoId) {
                    $promo  = Promo::find($promoId);
                    $diskon = $promo ? $promo->hitungDiskon($subtotal) : 0;
                }

                $total = $subtotal - $diskon;

                // Cek apakah kolom midtrans_order_id sudah ada (migration mungkin belum dijalankan)
                $midtransOrderId = null;
                try {
                    $midtransOrderId = $request->midtrans_order_id ?? null;
                } catch (\Exception $e) {
                    Log::warning('midtrans_order_id column issue: ' . $e->getMessage());
                }

                $dataTransaksi = [
                    'kode_transaksi' => Transaksi::generateKode(),
                    'karyawan_id'    => Auth::user()->id,
                    'promo_id'       => $promoId,
                    'subtotal'       => $subtotal,
                    'diskon'         => $diskon,
                    'total'          => $total,
                    'metode_bayar'   => $request->metode_bayar,
                ];

                // Tambahkan midtrans_order_id hanya kalau ada nilainya
                if ($midtransOrderId) {
                    $dataTransaksi['midtrans_order_id'] = $midtransOrderId;
                }

                $transaksi = Transaksi::create($dataTransaksi);
                $transaksi->details()->createMany($itemsSave);

                return $transaksi;
            });

            return redirect()
                ->route('karyawan.transaksi.struk', $transaksi->id)
                ->with('success', 'Transaksi berhasil disimpan!');

        } catch (\Exception $e) {
            Log::error('Transaksi store error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }

    public function struk(Transaksi $transaksi)
    {
        $transaksi->load(['details.varian.produk', 'karyawan', 'promo']);
        return view('karyawan.transaksi.struk', compact('transaksi'));
    }
}