<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_penjualan_detail', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_penjualan_detail', 'waktu_transaksi')) {
                $table->dateTime('waktu_transaksi')->nullable()->after('kode_penjualan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_penjualan_detail', function (Blueprint $table) {
            if (Schema::hasColumn('tb_penjualan_detail', 'waktu_transaksi')) {
                $table->dropColumn('waktu_transaksi');
            }
        });
    }
};
