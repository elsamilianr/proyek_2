<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'diajukan_oleh', 'disetujui_oleh',
        'nama_promo',        // ← sesuai CD
        'deskripsi',
        'diskon',            // ← sesuai CD (1 field)
        'tipe_diskon',       // persen / nominal (logika bisnis tetap ada)
        'min_pembelian',
        'tanggal_mulai',     // ← sesuai CD
        'tanggal_selesai',   // ← sesuai CD
        'status',            // ← sesuai CD
        'foto',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'diskon'          => 'decimal:2',
        'min_pembelian'   => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────────
    public function pengaju()   { return $this->belongsTo(User::class, 'diajukan_oleh'); }
    public function penyetuju() { return $this->belongsTo(User::class, 'disetujui_oleh'); }
    public function produks()   { return $this->belongsToMany(Produk::class, 'promo_produks'); }

    // ─── CD: validasiPromo() ─────────────────────────────────────────────────────
    public function validasiPromo(): bool
    {
        return $this->status === 'aktif'
            && now()->between($this->tanggal_mulai, $this->tanggal_selesai);
    }

    public function hitungDiskon(float $subtotal): float
    {
        if (! $this->validasiPromo()) return 0;
        if ($subtotal < $this->min_pembelian) return 0;

        if ($this->tipe_diskon === 'persen') {
            return $subtotal * ($this->diskon / 100);
        }
        return min($this->diskon, $subtotal);
    }

    public function scopeAktif($q)
    {
        return $q->where('status', 'aktif')
                 ->where('tanggal_mulai', '<=', now())
                 ->where('tanggal_selesai', '>=', now());
    }
}