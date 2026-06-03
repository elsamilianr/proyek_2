<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CD: Pembayaran
 * Atribut: id_pembayaran, id_pesanan, metode, status_pembayaran,
 *          bukti_pembayaran, tanggal_bayar, snap_token, midtrans_order_id
 * Method : prosesPembayaran(), uploadBukti(), konfirmasiPembayaran(),
 *          prosesMidtrans()
 */
class Pembayaran extends Model
{
    protected $fillable = [
        'id_pesanan',
        'diverifikasi_oleh',
        'metode',
        'status_pembayaran',
        'bukti_pembayaran',
        'jumlah_bayar',
        'tanggal_bayar',
        'catatan',
        'snap_token',          // ← Midtrans: token untuk Snap popup
        'midtrans_order_id',   // ← Midtrans: order ID yang dikirim ke Midtrans
    ];

    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'jumlah_bayar'  => 'decimal:2',
    ];

    public function pesanan()     { return $this->belongsTo(Pesanan::class, 'id_pesanan'); }
    public function verifikator() { return $this->belongsTo(User::class, 'diverifikasi_oleh'); }

    // ─── CD: prosesPembayaran() ───────────────────────────────────────────────────
    public function prosesPembayaran(string $metode, float $jumlah): void
    {
        $this->update([
            'metode'            => $metode,
            'jumlah_bayar'      => $jumlah,
            'status_pembayaran' => 'pending',
            'tanggal_bayar'     => now(),
        ]);
    }

    // ─── CD: uploadBukti() ───────────────────────────────────────────────────────
    public function uploadBukti(string $path): void
    {
        $this->update([
            'bukti_pembayaran'  => $path,
            'status_pembayaran' => 'menunggu_verifikasi',
        ]);
        $this->pesanan->ubahStatus('menunggu_verifikasi');
    }

    // ─── MIDTRANS: prosesMidtrans() ──────────────────────────────────────────────
    /**
     * Simpan snap_token setelah berhasil membuat transaksi Midtrans.
     */
    public function prosesMidtrans(string $snapToken, string $midtransOrderId): void
    {
        $this->update([
            'snap_token'        => $snapToken,
            'midtrans_order_id' => $midtransOrderId,
            'status_pembayaran' => 'pending',
            'tanggal_bayar'     => now(),
        ]);
    }

    // ─── CD: konfirmasiPembayaran() ──────────────────────────────────────────────
    public function konfirmasiPembayaran(int $karyawanId, bool $diterima, ?string $catatan = null): void
    {
        $this->update([
            'status_pembayaran' => $diterima ? 'terverifikasi' : 'ditolak',
            'diverifikasi_oleh' => $karyawanId,
            'catatan'           => $catatan,
            'tanggal_bayar'     => $diterima ? now() : $this->tanggal_bayar,
        ]);

        if ($diterima) {
            $this->pesanan->ubahStatus('diproses');
        } else {
            $this->pesanan->ubahStatus('menunggu_pembayaran');
        }
    }

    public function getLabelStatusAttribute(): string
    {
        return match ($this->status_pembayaran) {
            'pending'             => 'Menunggu Pembayaran',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'terverifikasi'       => 'Terverifikasi',
            'ditolak'             => 'Ditolak',
            default               => ucfirst($this->status_pembayaran),
        };
    }
}