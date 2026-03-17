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
        if (!Schema::hasTable('tb_pelanggan')) {
            Schema::create('tb_pelanggan', function (Blueprint $table) {
                $table->id('kode_pelanggan');
                $table->string('nama');
                $table->text('alamat');
                $table->string('telpon');
                $table->string('email')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pelanggan');
    }
};
