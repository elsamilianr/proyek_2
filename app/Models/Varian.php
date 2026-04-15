<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Varian extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_produk',        // ← sesuai CD
        'warna',
        'size',             // ← sesuai CD (bukan ukuran)
        'harga',
        'stok',
        'tanggal_restok',   // ← ditambah sesuai CD
        'sku',
    ];

    protected $casts = [
        'harga'          => 'decimal:2',
        'stok'           => 'integer',
        'tanggal_restok' => 'date',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────────
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    public function detailKeranjangs()
    {
        return $this->hasMany(DetailKeranjang::class, 'id_varian');
    }

    public function detailPesanans()
    {
        return $this->hasMany(DetailPesanan::class, 'id_varian');
    }

    public function detailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class, 'varian_id');
    }

    // ─── CD: updateStok() ─────────────────────────────────────────────────────────
    public function updateStok(int $jumlah, string $tipe = 'kurang'): void
    {
        if ($tipe === 'kurang') {
            if ($this->stok < $jumlah) {
                throw new \Exception("Stok {$this->produk->nama_produk} tidak mencukupi.");
            }
            $this->decrement('stok', $jumlah);
        } else {
            $this->increment('stok', $jumlah);
            $this->update(['tanggal_restok' => now()]);
        }
    }

    // Alias agar controller lama tetap jalan
    public function kurangiStok(int $jumlah): void { $this->updateStok($jumlah, 'kurang'); }
    public function tambahStok(int $jumlah): void  { $this->updateStok($jumlah, 'tambah'); }

    public function getLabelAttribute(): string
    {
        return implode(' / ', array_filter([$this->size, $this->warna]));
    }
}