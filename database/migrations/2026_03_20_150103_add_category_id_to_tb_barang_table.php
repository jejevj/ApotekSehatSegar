<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_barang', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('nama_barang');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });

        // Insert default category if it doesn't exist
        DB::table('categories')->insertOrIgnore([
            'id' => 1,
            'nama_kategori' => 'Belum Memiliki Kategori',
            'slug' => 'belum-memiliki-kategori',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update existing barangs to use category_id = 1
        DB::table('tb_barang')->whereNull('category_id')->update(['category_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_barang', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
