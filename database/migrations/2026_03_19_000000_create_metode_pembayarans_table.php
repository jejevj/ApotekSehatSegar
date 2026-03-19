<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('metode_pembayarans')) {
            Schema::create('metode_pembayarans', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->string('kode')->unique();
                $table->enum('tipe', ['tunai', 'transfer']);
                $table->string('nama_bank')->nullable();
                $table->string('no_rekening')->nullable();
                $table->boolean('is_aktif')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('metode_pembayarans');
    }
};

