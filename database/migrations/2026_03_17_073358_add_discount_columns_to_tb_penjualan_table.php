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
            $table->decimal('diskon_item', 5, 2)->default(0)->after('total'); // Diskon dalam persen
            $table->integer('potongan_item')->default(0)->after('diskon_item'); // Potongan dalam rupiah
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_penjualan', function (Blueprint $table) {
            $table->dropColumn(['diskon_item', 'potongan_item']);
        });
    }
};
