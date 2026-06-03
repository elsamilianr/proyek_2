<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // Tambah opsi 'midtrans' ke enum metode
            // MySQL: harus ubah definisi kolom
            $table->string('metode', 20)->default('transfer')->change();

            // Kolom tambahan untuk Midtrans
            $table->string('snap_token')->nullable()->after('jumlah_bayar');
            $table->string('midtrans_order_id')->nullable()->after('snap_token');
        });
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn(['snap_token', 'midtrans_order_id']);
            $table->enum('metode', ['transfer', 'qris', 'cash'])->default('transfer')->change();
        });
    }
};