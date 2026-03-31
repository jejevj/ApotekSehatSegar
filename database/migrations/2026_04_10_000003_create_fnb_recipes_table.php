<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fnb_recipes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('menu_id', 50); // FK ke tb_barang.kode_barcode
            $table->string('nama_resep', 150);
            $table->unsignedInteger('porsi')->default(1);
            $table->text('keterangan')->nullable();
            $table->unsignedInteger('hpp_per_porsi')->default(0);
            $table->boolean('hpp_outdated')->default(false);
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('menu_id')->references('kode_barcode')->on('tb_barang')->onDelete('cascade');
            $table->index(['store_id', 'menu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fnb_recipes');
    }
};
