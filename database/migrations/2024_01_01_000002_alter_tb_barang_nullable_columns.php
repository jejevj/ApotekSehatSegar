<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_barang', function (Blueprint $table) {
            if (Schema::hasColumn('tb_barang', 'rak_id')) {
                $table->unsignedBigInteger('rak_id')->nullable()->change();
            }

            if (Schema::hasColumn('tb_barang', 'isi')) {
                $table->integer('isi')->nullable()->change();
            }

            if (Schema::hasColumn('tb_barang', 'satuan')) {
                $table->string('satuan')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_barang', function (Blueprint $table) {
            if (Schema::hasColumn('tb_barang', 'rak_id')) {
                $table->unsignedBigInteger('rak_id')->nullable(false)->change();
            }

            if (Schema::hasColumn('tb_barang', 'isi')) {
                $table->integer('isi')->nullable(false)->change();
            }

            if (Schema::hasColumn('tb_barang', 'satuan')) {
                $table->string('satuan')->nullable(false)->change();
            }
        });
    }
};
