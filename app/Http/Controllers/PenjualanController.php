<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PenjualanController extends Controller
{
    public function index()
    {
        return view('penjualan.index');
    }

    public function data()
    {
        $penjualan = DB::table('tb_penjualan as p')
            ->leftJoin('users as u', 'p.id_user', '=', 'u.id')
            ->leftJoin('tb_pelanggan as pl', 'p.id_pelanggan', '=', 'pl.kode_pelanggan')
            ->select(
                'p.kode_penjualan',
                'p.tgl_penjualan as tanggal',
                'pl.nama as pelanggan_nama',
                'u.nama as user_nama',
                DB::raw('SUM(p.total) as total_harga')
            )
            ->groupBy('p.kode_penjualan', 'p.tgl_penjualan', 'pl.nama', 'u.nama')
            ->orderBy('p.tgl_penjualan', 'desc');

        return DataTables::of($penjualan)
            ->addIndexColumn()
            ->editColumn('total_harga', function ($row) {
                return 'Rp. ' . number_format($row->total_harga, 0, ',', '.');
            })
            ->addColumn('aksi', function ($row) {
                // Untuk sementara, kita gunakan kode_penjualan sebagai ID
                $showUrl = route('penjualan.show', $row->kode_penjualan);
                $destroyUrl = route('penjualan.destroy', $row->kode_penjualan);

                $viewButton = '<a href="' . $showUrl . '" class="btn btn-xs btn-info"><i class="material-icons">visibility</i></a>';
                $deleteButton = '<form action="' . $destroyUrl . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin ingin menghapus nota ini?\')">'
                                . csrf_field()
                                . method_field('DELETE')
                                . '<button type="submit" class="btn btn-xs btn-danger"><i class="material-icons">delete</i></button>'
                                . '</form>';
                return $viewButton . ' ' . $deleteButton;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function cetak(Request $request)
    {
        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;

        $penjualans = Penjualan::whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->get();

        return view('penjualan.cetak', compact('penjualans', 'tgl_awal', 'tgl_akhir'));
    }

    public function show($id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
