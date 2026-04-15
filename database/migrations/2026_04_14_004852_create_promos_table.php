<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diajukan_oleh')->constrained('users');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users');
            $table->string('nama_promo');                  // ← sesuai CD: nama_promo
            $table->text('deskripsi')->nullable();
            $table->decimal('diskon', 10, 2);              // ← sesuai CD: diskon (1 field)
            $table->enum('tipe_diskon', ['persen', 'nominal'])->default('persen'); // logika tetap ada
            $table->decimal('min_pembelian', 12, 2)->default(0);
            $table->date('tanggal_mulai');                 // ← sesuai CD
            $table->date('tanggal_selesai');               // ← sesuai CD
            $table->enum('status', ['pending', 'aktif', 'nonaktif', 'ditolak'])->default('pending'); // ← sesuai CD
            $table->string('foto')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('promo_produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')->constrained('promos')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_produks');
        Schema::dropIfExists('promos');
    }
};