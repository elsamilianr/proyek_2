<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = [
        'kode_transaksi',
        'karyawan_id',
        'promo_id',
        'subtotal',
        'diskon',
        'total',
        'metode_bayar',
        'uang_diterima',
        'kembalian',
        'catatan',
        'midtrans_order_id',   // ← order ID Midtrans (QRIS / GoPay / Kartu)
    ];

    protected $casts = [
        'subtotal'      => 'decimal:2',
        'diskon'        => 'decimal:2',
        'total'         => 'decimal:2',
        'uang_diterima' => 'decimal:2',
        'kembalian'     => 'decimal:2',
    ];

    // Semua metode Midtrans Snap (sama dengan checkout pembeli)
    const MIDTRANS_METHODS = ['qris', 'gopay', 'credit_card'];

    // Label tampilan per metode
    const LABEL_METODE = [
        'cash'        => 'Cash',
        'transfer'    => 'Transfer Bank',
        'qris'        => '📱 QRIS',
        'gopay'       => '💚 GoPay',
        'credit_card' => '💳 Kartu Kredit/Debit',
    ];

    public function karyawan()
    {
        return $this->belongsTo(User::class, 'karyawan_id');
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    public function details()
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public static function generateKode(): string
    {
        $prefix = 'TRX-' . now()->format('Ymd');
        $last   = static::where('kode_transaksi', 'like', $prefix . '%')->count();
        return $prefix . '-' . str_pad($last + 1, 3, '0', STR_PAD_LEFT);
    }

    public function isMidtrans(): bool
    {
        return in_array($this->metode_bayar, self::MIDTRANS_METHODS);
    }

    public function getLabelMetodeAttribute(): string
    {
        return self::LABEL_METODE[$this->metode_bayar] ?? strtoupper($this->metode_bayar);
    }
}