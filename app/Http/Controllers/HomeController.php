<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $jumlahBarang = Barang::count();
        $jumlahPelanggan = Pelanggan::count();
        $jumlahTransaksi = Penjualan::count();

        return view('home', compact('jumlahBarang', 'jumlahPelanggan', 'jumlahTransaksi'));
    }
}
