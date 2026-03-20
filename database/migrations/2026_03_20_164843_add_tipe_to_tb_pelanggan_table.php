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
        Schema::table('tb_pelanggan', function (Blueprint $table) {
            $table->enum('tipe', ['umum', 'khusus'])->default('umum')->after('nama');
        });

        // Ensure there is at least one "Umum" customer
        DB::table('tb_pelanggan')->insertOrIgnore([
            'kode_pelanggan' => 1,
            'nama' => 'Pelanggan Umum',
            'tipe' => 'umum',
            'alamat' => '-',
            'telpon' => '-',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_pelanggan', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};
