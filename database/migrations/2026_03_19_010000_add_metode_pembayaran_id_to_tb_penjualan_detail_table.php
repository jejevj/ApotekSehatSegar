<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_penjualan_detail', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_penjualan_detail', 'metode_pembayaran_id')) {
                $table->unsignedBigInteger('metode_pembayaran_id')->nullable()->after('kode_penjualan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_penjualan_detail', function (Blueprint $table) {
            if (Schema::hasColumn('tb_penjualan_detail', 'metode_pembayaran_id')) {
                $table->dropColumn('metode_pembayaran_id');
            }
        });
    }
};

