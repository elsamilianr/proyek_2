<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Keranjang = header (milik 1 user)
        Schema::create('keranjangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->timestamps();
        });

        // DetailKeranjang = item-item di dalam keranjang  ← sesuai CD
        Schema::create('detail_keranjangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_keranjang')
                  ->constrained('keranjangs')
                  ->onDelete('cascade');
            $table->foreignId('id_varian')
                  ->constrained('varians')
                  ->onDelete('cascade');
            $table->unsignedInteger('jumlah')->default(1);
            $table->timestamps();

            $table->unique(['id_keranjang', 'id_varian']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_keranjangs');
        Schema::dropIfExists('keranjangs');
    }
};