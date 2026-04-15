<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CD: Keranjang
 * Satu user punya satu keranjang (header).
 * Item-itemnya ada di DetailKeranjang.
 */
class Keranjang extends Model
{
    protected $fillable = ['id_user'];

    // ─── Relationships ────────────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /** CD: Keranjang 1 → 1..* DetailKeranjang */
    public function details()
    {
        return $this->hasMany(DetailKeranjang::class, 'id_keranjang');
    }

    // ─── CD: tambahItem() ─────────────────────────────────────────────────────────
    public function tambahItem(int $varianId, int $jumlah = 1): DetailKeranjang
    {
        $detail = $this->details()->where('id_varian', $varianId)->first();

        if ($detail) {
            $detail->increment('jumlah', $jumlah);
        } else {
            $detail = $this->details()->create([
                'id_varian' => $varianId,
                'jumlah'    => $jumlah,
            ]);
        }

        return $detail->fresh();
    }

    // ─── CD: ubahJumlah() ────────────────────────────────────────────────────────
    public function ubahJumlah(int $detailId, int $jumlah): void
    {
        $this->details()->where('id', $detailId)->update(['jumlah' => $jumlah]);
    }

    // ─── CD: hapusItem() ─────────────────────────────────────────────────────────
    public function hapusItem(int $detailId): void
    {
        $this->details()->where('id', $detailId)->delete();
    }

    // ─── CD: clearKeranjang() ────────────────────────────────────────────────────
    public function clearKeranjang(): void
    {
        $this->details()->delete();
    }

    public function getTotalAttribute(): float
    {
        return $this->details->sum(fn ($d) => $d->subtotal);
    }
}