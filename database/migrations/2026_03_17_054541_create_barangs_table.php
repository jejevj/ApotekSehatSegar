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
        if (!Schema::hasTable('tb_barang')) {
            Schema::create('tb_barang', function (Blueprint $table) {
                $table->string('kode_barcode', 50)->primary();
                $table->string('nama_barang');
                $table->string('satuan');
                $table->integer('harga_beli');
                $table->integer('stok');
                $table->integer('harga_jual');
                $table->integer('profit');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_barang');
    }
};
