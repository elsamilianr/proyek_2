<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Produk extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_produk',   // ← sesuai CD
        'slug',
        'deskripsi',
        'kategori',
        'foto',
        'is_aktif',
    ];

    protected $casts = ['is_aktif' => 'boolean'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($produk) {
            $produk->slug = $produk->slug ?? Str::slug($produk->nama_produk) . '-' . Str::random(5);
        });
    }

    // ─── Relationships ────────────────────────────────────────────────────────────

    /** CD: getVarian() */
    public function varians()
    {
        return $this->hasMany(Varian::class, 'id_produk');
    }

    public function fotoProduk()
    {
        return $this->hasMany(FotoProduk::class, 'produk_id');
    }

    public function promos()
    {
        return $this->belongsToMany(Promo::class, 'promo_produks');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────────
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────────
    public function getTotalStokAttribute(): int
    {
        return $this->varians->sum('stok');
    }

    public function getHargaMinAttribute()
    {
        return $this->varians->min('harga');
    }
}