<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // Hitung total valuasi aset (stok * harga beli) secara realtime
        $totalValuasiAset = Barang::sum(DB::raw('stok * harga_beli'));
        
        // Hitung total sisa hutang pembelian yang belum lunas
        $totalSisaHutang = \App\Models\Pembelian::where('status', 'belum_lunas')->sum('sisa');

        return view('home', compact('totalValuasiAset', 'totalSisaHutang'));
    }

    private function getDateRange(Request $request)
    {
        $filter = $request->query('filter', 'today');
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        if ($filter === '7_days') {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
        } elseif ($filter === '1_month') {
            $startDate = Carbon::now()->subMonth()->startOfDay();
        } elseif ($filter === '3_months') {
            $startDate = Carbon::now()->subMonths(3)->startOfDay();
        } elseif ($filter === 'custom') {
            $start = $request->query('start_date');
            $end = $request->query('end_date');
            if ($start && $end) {
                $startDate = Carbon::parse($start)->startOfDay();
                $endDate = Carbon::parse($end)->endOfDay();
            }
        }

        return [$startDate, $endDate];
    }

    public function summaryData(Request $request)
    {
        [$startDate, $endDate] = $this->getDateRange($request);

        $jumlahTransaksi = Penjualan::whereBetween('tgl_penjualan', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->count();
        
        $totalPendapatan = DB::table('tb_penjualan_detail')
            ->whereBetween('waktu_transaksi', [$startDate, $endDate])
            ->sum(DB::raw('IF(total_akhir > 0, total_akhir, total)'));

        $totalPajak = DB::table('tb_penjualan_detail')
            ->whereBetween('waktu_transaksi', [$startDate, $endDate])
            ->sum('pajak_nominal');

        $cogs = DB::table('tb_penjualan')
            ->join('tb_barang', 'tb_penjualan.kode_barcode', '=', 'tb_barang.kode_barcode')
            ->whereBetween('tb_penjualan.tgl_penjualan', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum(DB::raw('tb_penjualan.jumlah * tb_barang.harga_beli'));

        $keuntunganBersih = $totalPendapatan - $totalPajak - $cogs;

        $barangTerjual = Penjualan::whereBetween('tgl_penjualan', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->sum('jumlah');
        
        $pelangganAktif = Penjualan::whereBetween('tgl_penjualan', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereNotNull('id_pelanggan')
            ->distinct('id_pelanggan')
            ->count('id_pelanggan');

        return response()->json([
            'transaksi' => $jumlahTransaksi,
            'pendapatan' => $totalPendapatan ?? 0,
            'keuntungan_bersih' => $keuntunganBersih ?? 0,
            'barang_terjual' => $barangTerjual ?? 0,
            'pelanggan_aktif' => $pelangganAktif
        ]);
    }

    public function chartData(Request $request)
    {
        [$startDate, $endDate] = $this->getDateRange($request);

        // Ambil data penjualan yang di-group berdasarkan tanggal
        $penjualan = Penjualan::select(
                DB::raw('DATE(tgl_penjualan) as date'),
                DB::raw('COUNT(id) as total_transaksi'),
                DB::raw('SUM(total) as total_pendapatan')
            )
            ->whereBetween('tgl_penjualan', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $labels = [];
        $dataTransaksi = [];
        $dataPendapatan = [];

        // Fill missing dates with 0
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->translatedFormat('d M Y');
            
            $record = $penjualan->firstWhere('date', $dateStr);
            $dataTransaksi[] = $record ? $record->total_transaksi : 0;
            $dataPendapatan[] = $record ? $record->total_pendapatan : 0;

            $currentDate->addDay();
        }

        return response()->json([
            'labels' => $labels,
            'transaksi' => $dataTransaksi,
            'pendapatan' => $dataPendapatan
        ]);
    }

    public function transactionData(Request $request)
    {
        [$startDate, $endDate] = $this->getDateRange($request);

        // Ambil data dari tb_penjualan_detail yang memiliki waktu_transaksi
        $query = DB::table('tb_penjualan_detail')
            ->select('kode_penjualan', 'waktu_transaksi', 'total_akhir', 'total')
            ->whereBetween('waktu_transaksi', [$startDate, $endDate])
            ->orderBy('waktu_transaksi', 'desc');

        return \Yajra\DataTables\Facades\DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('waktu_transaksi', function ($row) {
                return Carbon::parse($row->waktu_transaksi)->translatedFormat('d F Y H:i');
            })
            ->addColumn('total_rp', function ($row) {
                $nilai = $row->total_akhir > 0 ? $row->total_akhir : $row->total;
                return 'Rp ' . number_format($nilai, 0, ',', '.');
            })
            ->addColumn('aksi', function ($row) {
                return '<a href="' . route('penjualan.show', $row->kode_penjualan) . '" class="btn btn-info btn-xs"><i class="material-icons">visibility</i> Detail</a>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}
