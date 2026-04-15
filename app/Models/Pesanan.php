<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB; // ← FIX: import DB facade

class Pesanan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_pesanan',
        'id_user',
        'promo_id',
        'tanggal',
        'subtotal',
        'diskon',
        'total_harga',
        'status_pesanan',
        'catatan',
    ];

    protected $casts = [
        'tanggal'     => 'date',
        'subtotal'    => 'decimal:2',
        'diskon'      => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────────
    public function pembeli()    { return $this->belongsTo(User::class, 'id_user'); }
    public function promo()      { return $this->belongsTo(Promo::class); }
    public function details()    { return $this->hasMany(DetailPesanan::class, 'id_pesanan'); }
    public function pembayaran() { return $this->hasOne(Pembayaran::class, 'id_pesanan'); }

    // ─── CD: generateID() ────────────────────────────────────────────────────────
    public static function generateID(): string
    {
        $prefix = 'KKH-' . now()->format('Ymd');
        $last   = static::where('kode_pesanan', 'like', $prefix . '%')->count();
        return $prefix . '-' . str_pad($last + 1, 3, '0', STR_PAD_LEFT);
    }

    // ─── CD: hitungTotal() ───────────────────────────────────────────────────────
    public function hitungTotal(): float
    {
        $subtotal = $this->details->sum('subtotal');
        $diskon   = $this->promo ? $this->promo->hitungDiskon($subtotal) : 0;
        return $subtotal - $diskon;
    }

    // ─── CD: ubahStatus() ────────────────────────────────────────────────────────
    public function ubahStatus(string $status): void
    {
        $valid = [
            'menunggu_pembayaran', 'menunggu_verifikasi',
            'diproses', 'selesai', 'dibatalkan',
        ];
        if (! in_array($status, $valid)) {
            throw new \InvalidArgumentException("Status '$status' tidak valid.");
        }
        $this->update(['status_pesanan' => $status]);
    }

    // ─── CD: checkout() ──────────────────────────────────────────────────────────
    public static function checkout(int $userId, array $items, ?int $promoId = null): self
    {
        return DB::transaction(function () use ($userId, $items, $promoId) { // ← FIX: DB:: bukan \DB::
            $pesanan = static::create([
                'kode_pesanan'   => static::generateID(),
                'id_user'        => $userId,
                'promo_id'       => $promoId,
                'tanggal'        => now(),
                'subtotal'       => 0,
                'diskon'         => 0,
                'total_harga'    => 0,
                'status_pesanan' => 'menunggu_pembayaran',
            ]);

            $subtotal = 0;
            foreach ($items as $item) {
                $varian       = Varian::lockForUpdate()->findOrFail($item['id_varian']);
                $varian->kurangiStok($item['jumlah']);

                $itemSubtotal  = $varian->harga * $item['jumlah'];
                $subtotal     += $itemSubtotal;

                $pesanan->details()->create([
                    'id_varian' => $varian->id,
                    'jumlah'    => $item['jumlah'],
                    'harga'     => $varian->harga,
                    'subtotal'  => $itemSubtotal,
                ]);
            }

            $diskon     = $promoId
                ? (Promo::find($promoId)?->hitungDiskon($subtotal) ?? 0)
                : 0;

            $pesanan->update([
                'subtotal'    => $subtotal,
                'diskon'      => $diskon,
                'total_harga' => $subtotal - $diskon,
            ]);

            return $pesanan;
        });
    }

    public function getLabelStatusAttribute(): string
    {
        return match ($this->status_pesanan) {
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'diproses'            => 'Sedang Diproses',
            'selesai'             => 'Selesai',
            'dibatalkan'          => 'Dibatalkan',
            default               => ucfirst($this->status_pesanan),
        };
    }
}