<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CD: DetailPesanan
 * Atribut: id_det_pesanan, id_pesanan, id_varian, jumlah, harga
 * Method : subtotal()
 */
class DetailPesanan extends Model
{
    protected $fillable = [
        'id_pesanan',   // ← sesuai CD
        'id_varian',    // ← sesuai CD
        'jumlah',
        'harga',        // ← sesuai CD: harga (snapshot saat order)
        'subtotal',
    ];

    protected $casts = [
        'harga'    => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function pesanan() { return $this->belongsTo(Pesanan::class, 'id_pesanan'); }
    public function varian()  { return $this->belongsTo(Varian::class, 'id_varian'); }

    /** CD: subtotal() */
    public function getSubtotalAttribute(): float
    {
        return $this->jumlah * $this->harga;
    }
}