<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama',        // ← sesuai CD
        'username',    // ← ditambah sesuai CD
        'email',
        'password',
        'role',
        'no_hp',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Dipakai oleh Breeze (login form pakai 'name') ───────────────────────────
    // Alias agar Breeze tetap kompatibel
    public function getNameAttribute(): string
    {
        return $this->nama;
    }

    // ─── Role helpers ─────────────────────────────────────────────────────────────
    public function isPemilik(): bool  { return $this->role === 'pemilik'; }
    public function isKaryawan(): bool { return $this->role === 'karyawan'; }
    public function isPembeli(): bool  { return $this->role === 'pembeli'; }

    // ─── Relationships ────────────────────────────────────────────────────────────
    public function keranjang()
    {
        return $this->hasOne(Keranjang::class, 'id_user');
    }

    public function pesanans()
    {
        return $this->hasMany(Pesanan::class, 'id_user');
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'karyawan_id');
    }

    public function promosDiajukan()
    {
        return $this->hasMany(Promo::class, 'diajukan_oleh');
    }
}