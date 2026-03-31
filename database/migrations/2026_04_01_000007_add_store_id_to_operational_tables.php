<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'tb_barang',
        'tb_penjualan',
        'tb_penjualan_detail',
        'pembelians',
        'pembelian_details',
        'pembayaran_pembelians',
        'tb_pelanggan',
        'distributors',
        'categories',
        'units',
        'raks',
        'metode_pembayarans',
        'stock_opnames',
        'stock_opname_items',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'store_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('store_id')->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'store_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('store_id');
                });
            }
        }
    }
};
