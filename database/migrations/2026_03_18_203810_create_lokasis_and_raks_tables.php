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
        Schema::create('tb_lokasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lokasi');
            $table->timestamps();
        });

        Schema::create('tb_rak', function (Blueprint $table) {
            $table->id();
            $table->string('nama_rak');
            $table->timestamps();
        });

        Schema::table('tb_barang', function (Blueprint $table) {
            $table->unsignedBigInteger('lokasi_id')->nullable()->after('unit_id');
            $table->unsignedBigInteger('rak_id')->nullable()->after('lokasi_id');

            $table->foreign('lokasi_id')->references('id')->on('tb_lokasi')->onDelete('set null');
            $table->foreign('rak_id')->references('id')->on('tb_rak')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_barang', function (Blueprint $table) {
            $table->dropForeign(['lokasi_id']);
            $table->dropForeign(['rak_id']);
            $table->dropColumn(['lokasi_id', 'rak_id']);
        });
        Schema::dropIfExists('tb_rak');
        Schema::dropIfExists('tb_lokasi');
    }
};
