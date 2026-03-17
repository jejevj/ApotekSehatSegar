<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(['id' => 1], [
            'nama_aplikasi' => 'Apotek Sehat Segar',
            'nama_pemilik' => 'Apoteker JAngga',
            'alamat' => 'Jl. Raya Sehat No. 123, Jakarta',
            'telepon' => '021-1234567',
            'email' => 'info@apoteksehat.com',
            'footer_text' => 'Copyright © 2026 Apotek Sehat Segar'
        ]);
    }
}
