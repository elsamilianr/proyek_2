<?php

namespace Database\Seeders;

use App\Models\Produk;
use App\Models\User;
use App\Models\Varian;
use App\Models\Keranjang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ─────────────────────────────────────────────────────────────────
        User::create([
            'nama'     => 'Pemilik Kiki',
            'username' => 'pemilik',
            'email'    => 'pemilik@kiki.com',
            'password' => Hash::make('password'),
            'role'     => 'pemilik',
            'no_hp'    => '081234567890',
        ]);

        User::create([
            'nama'     => 'Karyawan Toko',
            'username' => 'karyawan',
            'email'    => 'karyawan@kiki.com',
            'password' => Hash::make('password'),
            'role'     => 'karyawan',
            'no_hp'    => '082345678901',
        ]);

        $pembeli = User::create([
            'nama'     => 'Pembeli Contoh',
            'username' => 'pembeli',
            'email'    => 'pembeli@kiki.com',
            'password' => Hash::make('password'),
            'role'     => 'pembeli',
            'no_hp'    => '083456789012',
        ]);

        // Buat keranjang untuk pembeli
        Keranjang::create(['id_user' => $pembeli->id]);

        // ── Produk & Varian ───────────────────────────────────────────────────────
        // $katalog = [
        //     [
        //         'nama_produk' => 'Hijab Voal Motif Bunga',
        //         'kategori'    => 'Hijab',
        //         'deskripsi'   => 'Hijab voal premium dengan motif bunga cantik, nyaman dipakai sehari-hari.',
        //         'varians'     => [
        //             ['warna' => 'Putih',  'size' => null, 'harga' => 45000, 'stok' => 20],
        //             ['warna' => 'Pink',   'size' => null, 'harga' => 45000, 'stok' => 15],
        //             ['warna' => 'Hitam',  'size' => null, 'harga' => 45000, 'stok' => 25],
        //             ['warna' => 'Navy',   'size' => null, 'harga' => 45000, 'stok' => 10],
        //         ],
        //     ],
        //     [
        //         'nama_produk' => 'Gamis Syari Polos',
        //         'kategori'    => 'Gamis',
        //         'deskripsi'   => 'Gamis syari bahan Moscrepe, adem dan tidak transparan.',
        //         'varians'     => [
        //             ['warna' => 'Putih', 'size' => 'S',  'harga' => 185000, 'stok' => 8],
        //             ['warna' => 'Putih', 'size' => 'M',  'harga' => 185000, 'stok' => 12],
        //             ['warna' => 'Putih', 'size' => 'L',  'harga' => 185000, 'stok' => 10],
        //             ['warna' => 'Putih', 'size' => 'XL', 'harga' => 195000, 'stok' => 7],
        //             ['warna' => 'Hitam', 'size' => 'S',  'harga' => 185000, 'stok' => 9],
        //             ['warna' => 'Hitam', 'size' => 'M',  'harga' => 185000, 'stok' => 14],
        //             ['warna' => 'Hitam', 'size' => 'L',  'harga' => 185000, 'stok' => 6],
        //         ],
        //     ],
        //     [
        //         'nama_produk' => 'Kerudung Segi Empat Polos',
        //         'kategori'    => 'Hijab',
        //         'deskripsi'   => 'Kerudung segi empat bahan diamond, lembut dan mudah dibentuk.',
        //         'varians'     => [
        //             ['warna' => 'Cream',   'size' => null, 'harga' => 35000, 'stok' => 30],
        //             ['warna' => 'Abu-abu', 'size' => null, 'harga' => 35000, 'stok' => 25],
        //             ['warna' => 'Maroon',  'size' => null, 'harga' => 35000, 'stok' => 20],
        //             ['warna' => 'Cokelat', 'size' => null, 'harga' => 35000, 'stok' => 3],
        //         ],
        //     ],
        //     [
        //         'nama_produk' => 'Inner Ciput Anti Melar',
        //         'kategori'    => 'Aksesoris',
        //         'deskripsi'   => 'Inner ciput berbahan jersey premium, anti melar dan menyerap keringat.',
        //         'varians'     => [
        //             ['warna' => 'Hitam', 'size' => 'Free Size', 'harga' => 25000, 'stok' => 50],
        //             ['warna' => 'Putih', 'size' => 'Free Size', 'harga' => 25000, 'stok' => 45],
        //             ['warna' => 'Abu',   'size' => 'Free Size', 'harga' => 25000, 'stok' => 0],
        //         ],
        //     ],
        // ];

        // foreach ($katalog as $data) {
        //     $produk = Produk::create([
        //         'nama_produk' => $data['nama_produk'],
        //         'slug'        => Str::slug($data['nama_produk']) . '-' . Str::random(4),
        //         'deskripsi'   => $data['deskripsi'],
        //         'kategori'    => $data['kategori'],
        //         'is_aktif'    => true,
        //     ]);

        //     foreach ($data['varians'] as $i => $vd) {
        //         Varian::create([
        //             'id_produk' => $produk->id,
        //             'warna'     => $vd['warna'],
        //             'size'      => $vd['size'],
        //             'harga'     => $vd['harga'],
        //             'stok'      => $vd['stok'],
        //             'sku'       => strtoupper(Str::random(3)) . $produk->id . $i,
        //         ]);
        //     }
        // }
    }
}
