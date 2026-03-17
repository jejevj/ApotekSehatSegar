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
        if (!Schema::hasTable('tb_penjualan_detail')) {
            Schema::create('tb_penjualan_detail', function (Blueprint $table) {
                $table->id();
                $table->string('kode_penjualan', 50);
                $table->integer('bayar');
                $table->integer('kembali');
                $table->string('diskon', 100);
                $table->string('potongan', 100);
                $table->integer('total');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_penjualan_detail');
    }
};
