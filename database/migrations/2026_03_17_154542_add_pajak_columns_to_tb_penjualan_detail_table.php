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
            if (!Schema::hasColumn('tb_penjualan_detail', 'pajak_persen')) {
                $table->integer('pajak_persen')->default(0);
            }
            if (!Schema::hasColumn('tb_penjualan_detail', 'pajak_nominal')) {
                $table->integer('pajak_nominal')->default(0);
            }
            if (!Schema::hasColumn('tb_penjualan_detail', 'total_akhir')) {
                $table->integer('total_akhir')->default(0);
            }
            if (!Schema::hasColumn('tb_penjualan_detail', 'metode_pembayaran_id')) {
                $table->unsignedBigInteger('metode_pembayaran_id')->nullable()->after('kode_penjualan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_penjualan_detail', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('tb_penjualan_detail', 'pajak_persen')) {
                $columns[] = 'pajak_persen';
            }
            if (Schema::hasColumn('tb_penjualan_detail', 'pajak_nominal')) {
                $columns[] = 'pajak_nominal';
            }
            if (Schema::hasColumn('tb_penjualan_detail', 'total_akhir')) {
                $columns[] = 'total_akhir';
            }
            if (Schema::hasColumn('tb_penjualan_detail', 'metode_pembayaran_id')) {
                $columns[] = 'metode_pembayaran_id';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
