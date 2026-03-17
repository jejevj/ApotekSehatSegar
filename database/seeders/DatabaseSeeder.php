<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'username' => 'developer',
                'nama' => 'J Angga Wijaya',
                'password' => Hash::make('developer123'),
                'level' => 'admin',
                'foto' => 'je.jpg',
            ],
            [
                'username' => 'milla',
                'nama' => 'Milla Tina Hanifah',
                'password' => Hash::make('milla123'),
                'level' => 'kasir',
                'foto' => '1f04ed67f0cdb19bbf1f3d2a441bbeb7.jpg',
            ],
            [
                'username' => 'simon',
                'nama' => 'eka darmon',
                'password' => Hash::make('simon123'),
                'level' => 'admin',
                'foto' => 'bandar-527-ribu-pil-koplo-dibekuk-800-2019-10-23-135247_0.jpg',
            ],
            [
                'username' => 'farrel',
                'nama' => 'varrel trian rifandi',
                'password' => Hash::make('bapakkau'),
                'level' => 'kasir',
                'foto' => 'bandar-527-ribu-pil-koplo-dibekuk-800-2019-10-23-135247_0.jpg',
            ],
            [
                'username' => 'Indra',
                'nama' => 'Indra gusman',
                'password' => Hash::make('mamakkau'),
                'level' => 'kasir',
                'foto' => 'IMG_20211225_115907.jpg',
            ],
            [
                'username' => 'test',
                'nama' => 'test',
                'password' => Hash::make('test'),
                'level' => 'admin',
                'foto' => 'Screen Shot 2023-01-28 at 20.16.55.png',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['username' => $user['username']],
                $user
            );
        }

        $this->call([
            BarangSeeder::class,
            PenjualanTableSeeder::class,
        ]);
    }
}
