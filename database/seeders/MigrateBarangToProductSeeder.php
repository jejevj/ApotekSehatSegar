<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Barang;
use App\Models\Product;

class MigrateBarangToProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangs = Barang::all();
        foreach ($barangs as $barang) {
            if (!$barang->product_id) {
                $product = Product::create([
                    'nama_produk' => $barang->nama_barang,
                    'stok' => $barang->stok, // Asumsi stok saat ini sudah dalam unit terkecil
                ]);
                $barang->update(['product_id' => $product->id]);
            }
        }
    }
}
