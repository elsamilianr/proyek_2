<?php

use App\Http\Controllers\Pembeli\ProdukController as PembeliProdukController;
use App\Http\Controllers\Pembeli\KeranjangController;
use App\Http\Controllers\Pembeli\PesananController as PembeliPesananController;
use App\Http\Controllers\Karyawan\DashboardController;
use App\Http\Controllers\Karyawan\PesananController;
use App\Http\Controllers\Karyawan\PromoController;
use App\Http\Controllers\Karyawan\ProdukController;
use App\Http\Controllers\Karyawan\RiwayatController;
use App\Http\Controllers\Karyawan\TransaksiController;
use App\Http\Controllers\Karyawan\VarianController;
use App\Http\Controllers\Pembeli\MidtransController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ── Halaman publik ───────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (Auth::check()) {
        return match (Auth::user()->role) {
            'pemilik', 'karyawan' => redirect()->route('karyawan.dashboard'),
            default               => redirect()->route('pembeli.index'),
        };
    }
    return redirect()->route('pembeli.index');
})->name('home');

// ── Auth routes (Breeze) ─────────────────────────────────────────────────────────
require __DIR__ . '/auth.php';

// ── Redirect setelah login berdasarkan role ──────────────────────────────────────
Route::get('/dashboard', function () {
    $user = Auth::user();

    // defensive programming (biar gak error kalau null)
    if (!$user) {
        return redirect()->route('login');
    }

    return match ($user->role) {
        'pemilik'  => redirect()->route('pemilik.dashboard'),
        'karyawan' => redirect()->route('karyawan.dashboard'),
        'pembeli'  => redirect()->route('pembeli.index'),
        default    => redirect()->route('pembeli.index'),
    };
})->middleware(['auth'])->name('dashboard');


// ════════════════════════════════════════════════════════════════════════════════
//  KARYAWAN
// ════════════════════════════════════════════════════════════════════════════════
Route::prefix('karyawan')
    ->name('karyawan.')
    ->middleware(['auth', 'role:karyawan,pemilik'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // ── Produk (CRUD) ─────────────────────────────────────────────────────────
        Route::resource('produk', ProdukController::class);

        // ── Varian / Stok ─────────────────────────────────────────────────────────
        Route::get('/varian', [VarianController::class, 'index'])
            ->name('varian.index');
        Route::post('/produk/{produk}/varian', [VarianController::class, 'store'])
            ->name('varian.store');
        Route::put('/varian/{varian}', [VarianController::class, 'update'])
            ->name('varian.update');
        Route::patch('/varian/{varian}/tambah-stok', [VarianController::class, 'tambahStok'])
            ->name('varian.tambah-stok');
        Route::delete('/varian/{varian}', [VarianController::class, 'destroy'])
            ->name('varian.destroy');

        // ── Pesanan Online ────────────────────────────────────────────────────────
        Route::get('/pesanan', [PesananController::class, 'index'])
            ->name('pesanan.index');
        Route::get('/pesanan/{pesanan}', [PesananController::class, 'show'])
            ->name('pesanan.show');
        Route::patch('/pesanan/{pesanan}/verifikasi-pembayaran', [PesananController::class, 'verifikasiPembayaran'])
            ->name('pesanan.verifikasi-pembayaran');
        Route::patch('/pesanan/{pesanan}/status', [PesananController::class, 'updateStatus'])
            ->name('pesanan.update-status');

        // ── Transaksi Langsung (Kasir) ────────────────────────────────────────────
        Route::get('/transaksi/cari-varian', [TransaksiController::class, 'cariVarian'])
            ->name('transaksi.cari-varian');
        Route::get('/transaksi/kasir', [TransaksiController::class, 'create'])
            ->name('transaksi.create');
        Route::post('/transaksi/midtrans-token', [TransaksiController::class, 'getMidtransToken'])
            ->name('transaksi.midtrans-token');
        Route::post('/transaksi', [TransaksiController::class, 'store'])
            ->name('transaksi.store');
        Route::get('/transaksi/{transaksi}/struk', [TransaksiController::class, 'struk'])
            ->name('transaksi.struk');

        // ── Promo ─────────────────────────────────────────────────────────────────
        Route::get('/promo', [PromoController::class, 'index'])
            ->name('promo.index');
        Route::get('/promo/create', [PromoController::class, 'create'])
            ->name('promo.create');
        Route::post('/promo', [PromoController::class, 'store'])
            ->name('promo.store');
        Route::get('/promo/{promo}', [PromoController::class, 'show'])
            ->name('promo.show');
        Route::delete('/promo/{promo}', [PromoController::class, 'destroy'])
            ->name('promo.destroy');

        // ── Riwayat Transaksi ─────────────────────────────────────────────────────
        Route::get('/riwayat', [RiwayatController::class, 'index'])
            ->name('riwayat.index');
        Route::get('/riwayat/pesanan/{pesanan}', [RiwayatController::class, 'showPesanan'])
            ->name('riwayat.pesanan');
        Route::get('/riwayat/transaksi/{transaksi}', [RiwayatController::class, 'showTransaksi'])
            ->name('riwayat.transaksi');
    });


// ════════════════════════════════════════════════════════════════════════════════
//  PEMBELI (Storefront)
// ════════════════════════════════════════════════════════════════════════════════

// ── Produk publik (tanpa login) ───────────────────────────────────────────────
Route::middleware('pembeli.only')->group(function () {
    Route::get('/toko',                [PembeliProdukController::class, 'index'])->name('pembeli.index');
    Route::get('/toko/produk/{slug}',  [PembeliProdukController::class, 'show'])->name('pembeli.produk.show');
});

// ── Route yang butuh login ────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Keranjang — KeranjangController
    Route::get   ('/keranjang',           [KeranjangController::class, 'index']) ->name('pembeli.keranjang');
    Route::post  ('/keranjang',           [KeranjangController::class, 'tambah'])->name('pembeli.keranjang.tambah');
    Route::patch ('/keranjang/{detail}',  [KeranjangController::class, 'update'])->name('pembeli.keranjang.update');
    Route::delete('/keranjang/{detail}',  [KeranjangController::class, 'hapus']) ->name('pembeli.keranjang.hapus');

    // Pesanan — PesananController
    Route::post('/midtrans/token/{pesanan}',   [MidtransController::class,      'getSnapToken'])        ->name('midtrans.token');
    Route::post('/midtrans/token-pending',     [MidtransController::class,      'getSnapTokenPending']) ->name('midtrans.token.pending');
    Route::post('/midtrans/konfirmasi',        [PembeliPesananController::class, 'konfirmasiMidtrans']) ->name('midtrans.konfirmasi');
    Route::get ('/checkout',                  [PembeliPesananController::class, 'checkout'])          ->name('pembeli.checkout');
    Route::post('/pesanan',                   [PembeliPesananController::class, 'store'])             ->name('pembeli.pesanan.buat');
    Route::get ('/pesanan',                   [PembeliPesananController::class, 'index'])             ->name('pembeli.pesanan.index');
    Route::get ('/pesanan/{pesanan}',         [PembeliPesananController::class, 'show'])              ->name('pembeli.pesanan.show');

    // Pembayaran Transfer Bank (dari session, belum ada pesanan)
    Route::get ('/pembayaran',                [PembeliPesananController::class, 'formPembayaran'])    ->name('pembeli.pesanan.pembayaran.form');
    Route::post('/pembayaran',                [PembeliPesananController::class, 'uploadBukti'])       ->name('pembeli.pembayaran.upload');

    // Upload/perbarui bukti di halaman detail pesanan (pesanan sudah ada)
    Route::post('/pesanan/{pesanan}/bukti',   [PembeliPesananController::class, 'uploadBuktiPesanan'])->name('pembeli.pesanan.bukti');
});

// Webhook Midtrans — tidak pakai auth
Route::post('/midtrans/notification', [MidtransController::class, 'handleNotification'])
    ->name('midtrans.notification');