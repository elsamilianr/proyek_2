<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pesanan', 20)->unique();    // dari generateID()
            $table->foreignId('id_user')->constrained('users'); // ← sesuai CD
            $table->foreignId('promo_id')->nullable()->constrained('promos');
            $table->date('tanggal');                          // ← sesuai CD
            $table->decimal('subtotal', 12, 2);
            $table->decimal('diskon', 12, 2)->default(0);
            $table->decimal('total_harga', 12, 2);           // ← sesuai CD: total_harga
            $table->enum('status_pesanan', [                 // ← sesuai CD: status_pesanan
                'menunggu_pembayaran',
                'menunggu_verifikasi',
                'diproses',
                'selesai',
                'dibatalkan',
            ])->default('menunggu_pembayaran');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('detail_pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pesanan')->constrained('pesanans')->onDelete('cascade'); // ← sesuai CD
            $table->foreignId('id_varian')->constrained('varians');                        // ← sesuai CD
            $table->unsignedInteger('jumlah');
            $table->decimal('harga', 12, 2);      // ← sesuai CD: harga (bukan harga_satuan)
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pesanan')->constrained('pesanans')->onDelete('cascade'); // ← sesuai CD
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users');
            $table->enum('metode', ['transfer', 'qris', 'cash'])->default('transfer');    // ← sesuai CD
            $table->enum('status_pembayaran', [   // ← sesuai CD: status_pembayaran
                'pending', 'menunggu_verifikasi', 'terverifikasi', 'ditolak'
            ])->default('pending');
            $table->string('bukti_pembayaran')->nullable();  // ← sesuai CD
            $table->decimal('jumlah_bayar', 12, 2)->nullable();
            $table->timestamp('tanggal_bayar')->nullable();  // ← sesuai CD: tanggal_bayar
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
        Schema::dropIfExists('detail_pesanans');
        Schema::dropIfExists('pesanans');
    }
};