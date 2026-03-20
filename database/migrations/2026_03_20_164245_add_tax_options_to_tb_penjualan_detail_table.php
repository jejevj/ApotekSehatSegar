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
            $table->string('pajak_keterangan')->nullable()->after('pajak_nominal');
            $table->enum('pajak_ditanggung', ['toko', 'pembeli'])->default('pembeli')->after('pajak_keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_penjualan_detail', function (Blueprint $table) {
            $table->dropColumn(['pajak_keterangan', 'pajak_ditanggung']);
        });
    }
};
