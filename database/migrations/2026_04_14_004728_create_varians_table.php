<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('varians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_produk')               // ← sesuai CD: id_produk
                  ->constrained('produks')
                  ->onDelete('cascade');
            $table->string('warna', 50)->nullable();
            $table->string('size', 20)->nullable();       // ← sesuai CD: size (bukan ukuran)
            $table->decimal('harga', 12, 2);
            $table->unsignedInteger('stok')->default(0);
            $table->date('tanggal_restok')->nullable();   // ← ditambah sesuai CD
            $table->string('sku', 50)->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('foto_produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->string('foto');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foto_produks');
        Schema::dropIfExists('varians');
    }
};