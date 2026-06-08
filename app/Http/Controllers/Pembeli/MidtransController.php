<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class MidtransController extends Controller
{
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
            CURLOPT_HTTPHEADER     => [],  // ← tambahan ini untuk fix bug library
        ];
    }

    // ─── Token dari SESSION (sebelum pesanan dibuat di DB) ───────────────────────
    /**
     * Dipakai saat checkout Midtrans: data ada di session, pesanan belum ada di DB.
     * Buat token dengan order_id sementara (temp-{userId}-{time}).
     */
    public function getSnapTokenPending(Request $request): JsonResponse
    {
        $data = session('checkout_data');

        if (!$data || $data['metode'] !== 'midtrans') {
            return response()->json(['error' => 'Data checkout tidak ditemukan.'], 422);
        }

        $user    = Auth::user();
        $tempId  = 'TEMP-' . $user->id . '-' . time();

        // Hitung total dari items di session
        $subtotal = 0;
        $itemDetails = [];
        foreach ($data['items'] as $item) {
            $varian    = \App\Models\Varian::with('produk')->find($item['id_varian']);
            $itemTotal = ($varian->harga ?? 0) * $item['jumlah'];
            $subtotal += $itemTotal;
            $itemDetails[] = [
                'id'       => $item['id_varian'],
                'price'    => (int) ($varian->harga ?? 0),
                'quantity' => $item['jumlah'],
                'name'     => substr($varian->produk->nama_produk ?? 'Produk', 0, 50),
            ];
        }

        $diskon = 0;
        if (!empty($data['promo_id'])) {
            $promo  = \App\Models\Promo::find($data['promo_id']);
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

        try {
            $snapToken = Snap::getSnapToken($params);

            // Simpan temp order id ke session agar bisa dipakai konfirmasi
            session(['checkout_midtrans_temp_id' => $tempId]);

            return response()->json([
                'snap_token' => $snapToken,
                'client_key' => config('services.midtrans.client_key'),
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans getSnapTokenPending error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat token pembayaran.'], 500);
        }
    }

    public function getSnapToken(Request $request, int $pesananId): JsonResponse
    {
        $pesanan = Pesanan::with(['details.varian', 'pembeli'])
            ->where('id_user', Auth::id())
            ->findOrFail($pesananId);

        $pembayaran = Pembayaran::firstOrCreate(
            ['id_pesanan' => $pesanan->id],
            [
                'metode'            => 'midtrans',
                'status_pembayaran' => 'pending',
                'jumlah_bayar'      => $pesanan->total_harga,
            ]
        );

        if ($pembayaran->snap_token) {
            return response()->json([
                'snap_token' => $pembayaran->snap_token,
                'client_key' => config('services.midtrans.client_key'),
            ]);
        }

        $midtransOrderId = 'KKH-' . $pesanan->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id'     => $midtransOrderId,
                'gross_amount' => (int) $pesanan->total_harga,
            ],
            'customer_details' => [
                'first_name' => $pesanan->pembeli->name,
                'email'      => $pesanan->pembeli->email,
                'phone'      => $pesanan->pembeli->no_hp ?? '',
            ],
            'item_details' => $this->buildItemDetails($pesanan),
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            $pembayaran->prosesMidtrans($snapToken, $midtransOrderId);

            return response()->json([
                'snap_token' => $snapToken,
                'client_key' => config('services.midtrans.client_key'),
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans getSnapToken error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Gagal membuat token pembayaran. Silakan coba lagi.',
            ], 500);
        }
    }

    public function handleNotification(Request $request): JsonResponse
    {
        try {
            $notification = new Notification();

            $transactionStatus = $notification->transaction_status;
            $fraudStatus       = $notification->fraud_status;
            $orderId           = $notification->order_id;

            Log::info('Midtrans notification', [
                'order_id' => $orderId,
                'status'   => $transactionStatus,
                'fraud'    => $fraudStatus,
            ]);

            $pembayaran = Pembayaran::where('midtrans_order_id', $orderId)->firstOrFail();

            $this->updatePembayaranStatus($pembayaran, $transactionStatus, $fraudStatus);

            return response()->json(['status' => 'OK']);
        } catch (\Exception $e) {
            Log::error('Midtrans notification error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function updatePembayaranStatus(Pembayaran $pembayaran, string $transactionStatus, ?string $fraudStatus): void
    {
        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $pembayaran->update(['status_pembayaran' => 'terverifikasi', 'tanggal_bayar' => now()]);
                $pembayaran->pesanan->ubahStatus('diproses');
            } elseif ($fraudStatus === 'challenge') {
                $pembayaran->update(['status_pembayaran' => 'menunggu_verifikasi']);
            }
        } elseif ($transactionStatus === 'settlement') {
            $pembayaran->update(['status_pembayaran' => 'terverifikasi', 'tanggal_bayar' => now()]);
            $pembayaran->pesanan->ubahStatus('diproses');
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $pembayaran->update(['status_pembayaran' => 'ditolak']);
            $pembayaran->pesanan->ubahStatus('menunggu_pembayaran');
        } elseif ($transactionStatus === 'pending') {
            $pembayaran->update(['status_pembayaran' => 'pending']);
        }
    }

    private function buildItemDetails(Pesanan $pesanan): array
    {
        $items = $pesanan->details->map(fn($detail) => [
            'id'       => $detail->id_varian,
            'price'    => (int) $detail->harga,
            'quantity' => $detail->jumlah,
            'name'     => substr($detail->varian->produk->nama_produk ?? 'Produk', 0, 50),
        ])->toArray();

        if ($pesanan->diskon > 0) {
            $items[] = [
                'id'       => 'DISKON',
                'price'    => -(int) $pesanan->diskon,
                'quantity' => 1,
                'name'     => 'Diskon Promo',
            ];
        }

        return $items;
    }
}