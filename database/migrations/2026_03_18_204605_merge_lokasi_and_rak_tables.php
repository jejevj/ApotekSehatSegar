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
        // 1. Drop foreign keys and columns in tb_barang
        Schema::table('tb_barang', function (Blueprint $table) {
            $table->dropForeign(['lokasi_id']);
            $table->dropForeign(['rak_id']);
            $table->dropColumn(['lokasi_id', 'rak_id']);
        });

        // 2. Drop existing tables
        Schema::dropIfExists('tb_rak');
        Schema::dropIfExists('tb_lokasi');

        // 3. Create merged tb_rak table
        Schema::create('tb_rak', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lokasi');
            $table->string('nama_rak');
            $table->timestamps();
        });

        // 4. Add new rak_id to tb_barang
        Schema::table('tb_barang', function (Blueprint $table) {
            $table->unsignedBigInteger('rak_id')->nullable()->after('unit_id');
            $table->foreign('rak_id')->references('id')->on('tb_rak')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_barang', function (Blueprint $table) {
            $table->dropForeign(['rak_id']);
            $table->dropColumn(['rak_id']);
        });

        Schema::dropIfExists('tb_rak');

        // Recreate the separated tables
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
};
