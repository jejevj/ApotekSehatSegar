<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Penjualan;

class PenjualanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Penjualan::truncate();

        $data = [
            ['id' => 1, 'kode_penjualan' => 'PJ-8450540471', 'kode_barcode' => '3920409', 'jumlah' => 2, 'total' => 30000, 'tgl_penjualan' => '2020-12-22', 'id_pelanggan' => 3, 'id_user' => 1],
            ['id' => 2, 'kode_penjualan' => 'PJ-2169928605', 'kode_barcode' => '1240210', 'jumlah' => 2, 'total' => 200000, 'tgl_penjualan' => '2020-12-22', 'id_pelanggan' => 3, 'id_user' => 1],
            ['id' => 3, 'kode_penjualan' => 'PJ-2169928605', 'kode_barcode' => '3920409', 'jumlah' => 2, 'total' => 30000, 'tgl_penjualan' => '2020-12-22', 'id_pelanggan' => 3, 'id_user' => 1],
            ['id' => 6, 'kode_penjualan' => 'PJ-3466996384', 'kode_barcode' => '1240210', 'jumlah' => 1, 'total' => 100000, 'tgl_penjualan' => '2020-12-23', 'id_pelanggan' => 3, 'id_user' => 1],
            ['id' => 7, 'kode_penjualan' => 'PJ-3466996384', 'kode_barcode' => '1240210', 'jumlah' => 1, 'total' => 100000, 'tgl_penjualan' => '2020-12-23', 'id_pelanggan' => 3, 'id_user' => 1],
            ['id' => 8, 'kode_penjualan' => 'PJ-3466996384', 'kode_barcode' => '1240210', 'jumlah' => 1, 'total' => 100000, 'tgl_penjualan' => '2020-12-23', 'id_pelanggan' => 3, 'id_user' => 1],
            ['id' => 9, 'kode_penjualan' => 'PJ-1354913682', 'kode_barcode' => '1240210', 'jumlah' => 1, 'total' => 100000, 'tgl_penjualan' => '2020-12-23', 'id_pelanggan' => 3, 'id_user' => 1],
            ['id' => 10, 'kode_penjualan' => 'PJ-9192041736', 'kode_barcode' => '1240210', 'jumlah' => 1, 'total' => 100000, 'tgl_penjualan' => '2020-12-25', 'id_pelanggan' => 3, 'id_user' => 1],
            ['id' => 11, 'kode_penjualan' => 'PJ-0352964082', 'kode_barcode' => '990234803294', 'jumlah' => 2, 'total' => 200000, 'tgl_penjualan' => '2021-12-09', 'id_pelanggan' => 3, 'id_user' => 1],
            ['id' => 12, 'kode_penjualan' => 'PJ-0162811611', 'kode_barcode' => '8994254011115', 'jumlah' => 1, 'total' => 15000, 'tgl_penjualan' => '2021-12-14', 'id_pelanggan' => 3, 'id_user' => 1],
            ['id' => 13, 'kode_penjualan' => 'PJ-1112811939', 'kode_barcode' => '8997014182063', 'jumlah' => 0, 'total' => 0, 'tgl_penjualan' => '2021-12-15', 'id_pelanggan' => 3, 'id_user' => 1],
            ['id' => 14, 'kode_penjualan' => 'PJ-4786196925', 'kode_barcode' => '8993498210261', 'jumlah' => 1, 'total' => 15000, 'tgl_penjualan' => '2021-12-15', 'id_pelanggan' => 3, 'id_user' => 1],
            ['id' => 15, 'kode_penjualan' => 'PJ-4786196925', 'kode_barcode' => '8992867495544', 'jumlah' => 1, 'total' => 18000, 'tgl_penjualan' => '2021-12-15', 'id_pelanggan' => 3, 'id_user' => 1],
            ['id' => 16, 'kode_penjualan' => 'PJ-4786196925', 'kode_barcode' => '4987188567210', 'jumlah' => 1, 'total' => 10000, 'tgl_penjualan' => '2021-12-15', 'id_pelanggan' => 3, 'id_user' => 1],
            ['id' => 17, 'kode_penjualan' => 'PJ-4786196925', 'kode_barcode' => '4987188567210', 'jumlah' => 1, 'total' => 10000, 'tgl_penjualan' => '2021-12-15', 'id_pelanggan' => 3, 'id_user' => 1],
        ];

        foreach ($data as $item) {
            Penjualan::create($item);
        }
    }
}
