<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_barang', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_barang', 'unit_id')) {
                $table->unsignedBigInteger('unit_id')->nullable()->after('nama_barang');
            }
            if (!Schema::hasColumn('tb_barang', 'isi')) {
                $table->integer('isi')->default(1)->after('unit_id');
            }
        });

        $dbName = DB::getDatabaseName();
        $fkExists = DB::table('information_schema.table_constraints')
            ->where('constraint_schema', $dbName)
            ->where('table_name', 'tb_barang')
            ->where('constraint_name', 'tb_barang_unit_id_foreign')
            ->exists();

        if (!$fkExists) {
            Schema::table('tb_barang', function (Blueprint $table) {
                $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        $dbName = DB::getDatabaseName();
        $fkExists = DB::table('information_schema.table_constraints')
            ->where('constraint_schema', $dbName)
            ->where('table_name', 'tb_barang')
            ->where('constraint_name', 'tb_barang_unit_id_foreign')
            ->exists();

        Schema::table('tb_barang', function (Blueprint $table) {
            if ($fkExists) {
                $table->dropForeign(['unit_id']);
            }
            $table->dropColumn(['unit_id', 'isi']);
        });
    }
};
