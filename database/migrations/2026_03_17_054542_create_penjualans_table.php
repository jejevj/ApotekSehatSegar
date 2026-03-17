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
        if (!Schema::hasTable('tb_penjualan')) {
            Schema::create('tb_penjualan', function (Blueprint $table) {
                $table->id();
                $table->string('kode_penjualan');
                $table->string('kode_barcode');
                $table->integer('jumlah');
                $table->integer('total');
                $table->date('tgl_penjualan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_penjualan');
    }
};
