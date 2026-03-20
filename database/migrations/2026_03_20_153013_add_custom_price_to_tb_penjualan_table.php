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
        Schema::table('tb_penjualan', function (Blueprint $table) {
            $table->integer('harga_jual_kustom')->nullable()->after('kode_barcode');
            $table->unsignedBigInteger('user_id_pengubah')->nullable()->after('harga_jual_kustom');
            $table->foreign('user_id_pengubah')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_penjualan', function (Blueprint $table) {
            $table->dropForeign(['user_id_pengubah']);
            $table->dropColumn(['harga_jual_kustom', 'user_id_pengubah']);
        });
    }
};
