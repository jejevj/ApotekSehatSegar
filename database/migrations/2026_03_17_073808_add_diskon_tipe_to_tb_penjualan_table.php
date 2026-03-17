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
            $table->string('diskon_tipe', 10)->default('persen')->after('diskon_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_penjualan', function (Blueprint $table) {
            $table->dropColumn('diskon_tipe');
        });
    }
};
