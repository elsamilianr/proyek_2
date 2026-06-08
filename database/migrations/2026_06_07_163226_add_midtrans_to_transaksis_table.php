<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // Simpan order_id Midtrans agar bisa dicocokkan dengan notifikasi
            $table->string('midtrans_order_id', 60)->nullable()->after('catatan');
        });

        // Tambahkan 'gopay' dan 'credit_card' ke enum metode_bayar
        // (SQLite: enum adalah string biasa, tidak perlu alter)
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement(
                "ALTER TABLE transaksis MODIFY COLUMN metode_bayar
                 ENUM('cash','transfer','qris','gopay','credit_card')
                 NOT NULL DEFAULT 'cash'"
            );
        }
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn('midtrans_order_id');
        });

        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement(
                "ALTER TABLE transaksis MODIFY COLUMN metode_bayar
                 ENUM('cash','transfer','qris')
                 NOT NULL DEFAULT 'cash'"
            );
        }
    }
};