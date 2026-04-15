<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CD: DetailKeranjang
 * Setiap baris = 1 varian produk di dalam keranjang.
 */
class DetailKeranjang extends Model
{
    protected $table    = 'detail_keranjangs';
    protected $fillable = ['id_keranjang', 'id_varian', 'jumlah'];

    // ─── Relationships ────────────────────────────────────────────────────────────
    public function keranjang()
    {
        return $this->belongsTo(Keranjang::class, 'id_keranjang');
    }

    public function varian()
    {
        return $this->belongsTo(Varian::class, 'id_varian');
    }

    // ─── CD: subtotal() ──────────────────────────────────────────────────────────
    public function getSubtotalAttribute(): float
    {
        return $this->jumlah * ($this->varian->harga ?? 0);
    }
}