<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tb_penjualan')
            ->whereNull('id_user')
            ->update(['id_user' => 1]);

        DB::table('tb_penjualan')
            ->whereNull('id_pelanggan')
            ->update(['id_pelanggan' => 3]);
    }
}
