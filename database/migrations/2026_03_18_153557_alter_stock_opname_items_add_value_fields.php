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
        Schema::table('stock_opname_items', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_opname_items', 'harga_beli')) {
                $table->bigInteger('harga_beli')->default(0)->after('nama_barang');
            }
            if (!Schema::hasColumn('stock_opname_items', 'nilai_selisih')) {
                $table->bigInteger('nilai_selisih')->default(0)->after('selisih');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_opname_items', function (Blueprint $table) {
            $table->dropColumn(['harga_beli', 'nilai_selisih']);
        });
    }
};
