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
    ];

    protected $casts = [
        'subtotal'      => 'decimal:2',
        'diskon'        => 'decimal:2',
        'total'         => 'decimal:2',
        'uang_diterima' => 'decimal:2',
        'kembalian'     => 'decimal:2',
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
}