<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Barang;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Models\MetodePembayaran;

class PenjualanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:penjualan.view')->only('index', 'data', 'show');
        $this->middleware('permission:penjualan.create')->only('create', 'addItem', 'addFromModal', 'updateItem', 'removeItem', 'storeDetail', 'cancel');
        $this->middleware('permission:penjualan.delete')->only('destroy');
        $this->middleware('permission:penjualan.print_struk')->only('cetakStruk');
        $this->middleware('permission:laporan.print')->only('cetak');
    }

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
                $buttons = '';
                if (auth()->user()->hasPermission('penjualan.view')) {
                    $buttons .= '<a href="' . route('penjualan.show', $row->kode_penjualan) . '" class="btn btn-xs btn-info"><i class="material-icons">visibility</i></a>';
                }
                if (auth()->user()->hasPermission('penjualan.print_struk')) {
                    $buttons .= ' <a href="' . route('penjualan.cetakStruk', ['kode_pjl' => $row->kode_penjualan]) . '" target="_blank" class="btn btn-xs btn-success"><i class="material-icons">print</i></a>';
                }
                if (auth()->user()->hasPermission('penjualan.delete')) {
                    $buttons .= ' <form action="' . route('penjualan.destroy', $row->kode_penjualan) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin ingin menghapus nota ini?\')">'
                                . csrf_field()
                                . method_field('DELETE')
                                . '<button type="submit" class="btn btn-xs btn-danger"><i class="material-icons">delete</i></button>'
                                . '</form>';
                }
                return $buttons;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create(Request $request)
    {
        // Jika tidak ada kode penjualan di URL, buat baru dan redirect
        if (!$request->has('kodepj')) {
            $newKodePenjualan = 'PJ-' . rand(1000000000, 9999999999);
            return redirect()->route('penjualan.create', ['kodepj' => $newKodePenjualan]);
        }

        $kode_penjualan = $request->query('kodepj');
        
        $items = Penjualan::with('barang')
            ->where('kode_penjualan', $kode_penjualan)
            ->get();
            
        $pelanggan = Pelanggan::all();
        $total_bayar = $items->sum('total');
        $metode_pembayaran = MetodePembayaran::where('is_aktif', true)->orderBy('nama')->get();
        
        return view('penjualan.create', compact('kode_penjualan', 'items', 'pelanggan', 'total_bayar', 'metode_pembayaran'));
    }

    public function addItem(Request $request)
    {
        $barcode = $request->kode_barcode;
        $kode_penjualan = $request->kode_penjualan;
        
        $barang = Barang::where('kode_barcode', $barcode)->first();
        if (!$barang) {
            return redirect()->route('penjualan.create', ['kodepj' => $kode_penjualan])->with('error', 'Barang tidak ditemukan');
        }
        
        if ($barang->stok <= 0) {
            return redirect()->route('penjualan.create', ['kodepj' => $kode_penjualan])->with('error', 'Stok barang habis');
        }
        
        $jumlah = 1;
        
        // Cek apakah item sudah ada di keranjang
        $existingItem = Penjualan::where('kode_penjualan', $kode_penjualan)
                                ->where('kode_barcode', $barcode)
                                ->first();

        if ($existingItem) {
            // Jika sudah ada, tambahkan jumlahnya
            $existingItem->increment('jumlah', $jumlah);
            $newTotal = $existingItem->jumlah * $barang->harga_jual;
            $existingItem->update(['total' => $newTotal]);
        } else {
            // Jika belum ada, buat item baru
            $total = $jumlah * $barang->harga_jual;
            $now = Carbon::now();
            Penjualan::create([
                'kode_penjualan' => $kode_penjualan,
                'kode_barcode' => $barcode,
                'jumlah' => $jumlah,
                'total' => $total,
                'tgl_penjualan' => $now->toDateString(),
                'id_pelanggan' => 3, // Default Pelanggan Biasa
                'id_user' => auth()->id(),
                'diskon_tipe' => 'rupiah',
            ]);
        }
        
        // Kurangi stok
        $barang->decrement('stok', $jumlah);
        
        return redirect()->route('penjualan.create', ['kodepj' => $kode_penjualan]);
    }

    public function addFromModal(Request $request)
    {
        $barcode = $request->kode_barcode;
        $jumlah = $request->jumlah;
        $kode_penjualan = $request->kode_penjualan;

        $barang = Barang::where('kode_barcode', $barcode)->first();
        if (!$barang) {
            return response()->json(['error' => 'Barang tidak ditemukan'], 404);
        }

        if ($barang->stok < $jumlah) {
            return response()->json(['error' => 'Stok tidak mencukupi'], 400);
        }

        $existingItem = Penjualan::where('kode_penjualan', $kode_penjualan)
                                ->where('kode_barcode', $barcode)
                                ->first();

        if ($existingItem) {
            $existingItem->increment('jumlah', $jumlah);
            $newTotal = $existingItem->jumlah * $barang->harga_jual;
            $existingItem->update(['total' => $newTotal]);
        } else {
            $total = $jumlah * $barang->harga_jual;
            $now = Carbon::now();
            Penjualan::create([
                'kode_penjualan' => $kode_penjualan,
                'kode_barcode' => $barcode,
                'jumlah' => $jumlah,
                'total' => $total,
                'tgl_penjualan' => $now->toDateString(),
                'id_pelanggan' => 3, // Default Pelanggan Biasa
                'id_user' => auth()->id(),
                'diskon_tipe' => 'rupiah',
            ]);
        }

        $barang->decrement('stok', $jumlah);

        return response()->json(['success' => 'Item berhasil ditambahkan']);
    }

    public function updateItem(Request $request)
    {
        $id = $request->id;
        $field = $request->field;
        $value = $request->value;

        $item = Penjualan::findOrFail($id);
        $barang = $item->barang;

        $oldValue = $item->{$field};

        // Validasi stok untuk field jumlah
        if ($field == 'jumlah') {
            $stokDifference = $value - $oldValue;
            if ($barang->stok < $stokDifference) {
                return response()->json(['error' => 'Stok tidak mencukupi'], 400);
            }
            $barang->decrement('stok', $stokDifference);
        }

        // Hanya update field yang diminta
        if ($field == 'diskon_item') {
            $diskonValue = ($value === null || $value === '') ? 0 : (int) $value;
            $item->diskon_item = $diskonValue;
            if ($request->has('diskon_tipe')) {
                $item->diskon_tipe = $request->diskon_tipe;
            }
        } elseif ($field == 'jumlah') {
            $qtyValue = ($value === null || $value === '') ? 1 : (int) $value;
            if ($qtyValue < 1) {
                return response()->json(['error' => 'Jumlah minimal 1'], 400);
            }
            $item->jumlah = $qtyValue;
        }

        // Hitung ulang total berdasarkan data terbaru di item
        $harga_jual = $barang->harga_jual;
        $potongan = 0;
        if ($item->diskon_tipe == 'persen') {
            $potongan = ($harga_jual * $item->diskon_item) / 100;
        } else { // rupiah
            $potongan = $item->diskon_item;
        }

        // Validasi diskon tidak boleh lebih besar dari harga
        if ($potongan > $harga_jual) { // Validasi per item, bukan total
            return response()->json(['error' => 'Diskon per item tidak boleh lebih besar dari harga jualnya'], 400);
        }
        
        $item->potongan_item = $potongan;
        $item->total = ($harga_jual - $potongan) * $item->jumlah;
        $item->save();

        return response()->json(['success' => 'Item berhasil diupdate']);
    }

    public function removeItem($id)
    {
        $item = Penjualan::findOrFail($id);
        $kode_penjualan = $item->kode_penjualan;
        
        // Kembalikan stok
        $barang = Barang::where('kode_barcode', $item->kode_barcode)->first();
        if ($barang) {
            $barang->increment('stok', $item->jumlah);
        }
        
        $item->delete();
        
        return redirect()->route('penjualan.create', ['kodepj' => $kode_penjualan])->with('success', 'Item dihapus');
    }

    public function storeDetail(Request $request)
    {
        $kode_penjualan = $request->kode_penjualan;
        $now = Carbon::now();

        $metodePembayaranId = $request->metode_pembayaran_id;
        if ($metodePembayaranId) {
            $exists = MetodePembayaran::where('is_aktif', true)->whereKey($metodePembayaranId)->exists();
            if (!$exists) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Metode pembayaran tidak valid'], 400);
                }
                return redirect()->route('penjualan.create', ['kodepj' => $kode_penjualan])->with('error', 'Metode pembayaran tidak valid');
            }
        }

        $totalDb = (int) Penjualan::where('kode_penjualan', $kode_penjualan)->sum('total');
        $diskonGlobal = (int) ($request->diskon ?? 0);
        if ($diskonGlobal < 0) {
            $diskonGlobal = 0;
        }
        if ($diskonGlobal > $totalDb) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Diskon tidak boleh lebih besar dari Total'], 400);
            }
            return redirect()->route('penjualan.create', ['kodepj' => $kode_penjualan])->with('error', 'Diskon tidak boleh lebih besar dari Total');
        }

        $subTotal = $totalDb - $diskonGlobal;

        $pajakAktif = (int) ($request->pajak_aktif ?? 0) === 1;
        $pajakPersen = $pajakAktif ? (int) ($request->pajak_persen ?? 0) : 0;
        if ($pajakPersen < 0) {
            $pajakPersen = 0;
        }
        if ($pajakPersen > 100) {
            $pajakPersen = 100;
        }
        $pajakNominal = (int) round(($subTotal * $pajakPersen) / 100);
        $totalAkhir = $subTotal + $pajakNominal;

        $bayar = (int) ($request->bayar ?? 0);
        if ($bayar < 0) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Bayar tidak boleh minus'], 400);
            }
            return redirect()->route('penjualan.create', ['kodepj' => $kode_penjualan])->with('error', 'Bayar tidak boleh minus');
        }
        if ($bayar < $totalAkhir) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Bayar tidak boleh kurang dari Total Akhir'], 400);
            }
            return redirect()->route('penjualan.create', ['kodepj' => $kode_penjualan])->with('error', 'Bayar tidak boleh kurang dari Total Akhir');
        }
        $kembali = $bayar - $totalAkhir;
        
        // Update all items for this kode_penjualan with the selected customer
        Penjualan::where('kode_penjualan', $kode_penjualan)->update([
            'id_pelanggan' => $request->id_pelanggan,
            'id_user' => auth()->id()
        ]);
        
        DB::table('tb_penjualan_detail')->updateOrInsert(
            ['kode_penjualan' => $kode_penjualan],
            [
                'waktu_transaksi' => $now->toDateTimeString(),
                'bayar' => $bayar,
                'kembali' => $kembali,
                'diskon' => $diskonGlobal,
                'potongan' => $diskonGlobal,
                'pajak_persen' => $pajakPersen,
                'pajak_nominal' => $pajakNominal,
                'total' => $totalDb,
                'total_akhir' => $totalAkhir,
                'metode_pembayaran_id' => $metodePembayaranId,
            ]
        );

        if ($request->expectsJson() && $request->action_type === 'print') {
            return response()->json([
                'print_url' => route('penjualan.cetakStruk', ['kode_pjl' => $kode_penjualan]),
            ]);
        }

        if ($request->action_type === 'print') {
            return redirect()->route('penjualan.cetakStruk', ['kode_pjl' => $kode_penjualan]);
        }

        return redirect()->route('penjualan.index')->with('success', 'Transaksi berhasil disimpan');
    }

    public function cancel(Request $request)
    {
        $kode_penjualan = $request->kode_penjualan;

        $items = Penjualan::where('kode_penjualan', $kode_penjualan)->get();
        foreach ($items as $item) {
            $barang = Barang::where('kode_barcode', $item->kode_barcode)->first();
            if ($barang) {
                $barang->increment('stok', $item->jumlah);
            }
        }

        Penjualan::where('kode_penjualan', $kode_penjualan)->delete();
        DB::table('tb_penjualan_detail')->where('kode_penjualan', $kode_penjualan)->delete();

        return redirect()->route('penjualan.index')->with('success', 'Transaksi dibatalkan');
    }

    public function cetak(Request $request)
    {
        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;

        $penjualans = Penjualan::whereBetween('tgl_penjualan', [$tgl_awal, $tgl_akhir])->get();

        return view('penjualan.cetak', compact('penjualans', 'tgl_awal', 'tgl_akhir'));
    }

    public function cetakStruk(Request $request)
    {
        $kode_pj = $request->query('kode_pjl');
        
        $penjualan = Penjualan::where('kode_penjualan', $kode_pj)->first();
        if (!$penjualan) {
            return "Nota tidak ditemukan";
        }
        
        $pelanggan = Pelanggan::where('kode_pelanggan', $penjualan->id_pelanggan)->first();
        $user = auth()->user();
        $detail = DB::table('tb_penjualan_detail')->where('kode_penjualan', $kode_pj)->first();
        $metodePembayaranNama = 'Tunai';
        if (!empty($detail?->metode_pembayaran_id)) {
            $mp = MetodePembayaran::find($detail->metode_pembayaran_id);
            if ($mp && $mp->is_aktif) {
                $metodePembayaranNama = $mp->nama;
            }
        }
        
        $items = DB::table('tb_penjualan as p')
            ->join('tb_barang as b', 'p.kode_barcode', '=', 'b.kode_barcode')
            ->join('tb_penjualan_detail as pd', 'p.kode_penjualan', '=', 'pd.kode_penjualan')
            ->where('p.kode_penjualan', $kode_pj)
            ->select(
                'p.id',
                'p.kode_penjualan',
                'p.kode_barcode',
                'p.jumlah',
                'p.tgl_penjualan',
                'p.diskon_tipe',
                'p.diskon_item',
                'p.potongan_item',
                'p.total as line_total',
                'b.nama_barang',
                'b.harga_jual',
                'pd.waktu_transaksi',
                'pd.bayar',
                'pd.kembali',
                'pd.diskon as diskon_global',
                'pd.potongan as potongan_global',
                'pd.pajak_persen',
                'pd.pajak_nominal',
                'pd.total as total_global',
                'pd.total_akhir'
            )
            ->get();
            
        return view('penjualan.cetak_struk', compact('penjualan', 'pelanggan', 'user', 'items', 'detail', 'metodePembayaranNama'));
    }

    public function show($id)
    {
        $penjualan = DB::table('tb_penjualan as p')
            ->leftJoin('tb_pelanggan as pl', 'p.id_pelanggan', '=', 'pl.kode_pelanggan')
            ->leftJoin('users as u', 'p.id_user', '=', 'u.id')
            ->where('p.kode_penjualan', $id)
            ->select(
                'p.kode_penjualan',
                'p.tgl_penjualan',
                'pl.nama as pelanggan_nama',
                'u.nama as kasir_nama'
            )
            ->first();

        $items = Penjualan::with('barang')
            ->where('kode_penjualan', $id)
            ->get();

        $detail = DB::table('tb_penjualan_detail')
            ->where('kode_penjualan', $id)
            ->first();

        $metodePembayaranNama = 'Tunai';
        if (!empty($detail?->metode_pembayaran_id)) {
            $mp = MetodePembayaran::find($detail->metode_pembayaran_id);
            if ($mp && $mp->is_aktif) {
                $metodePembayaranNama = $mp->nama;
            }
        }

        return view('penjualan.show', compact('penjualan', 'items', 'detail', 'metodePembayaranNama'));
    }

    public function destroy($id)
    {
        // $id is kode_penjualan here
        Penjualan::where('kode_penjualan', $id)->delete();
        DB::table('tb_penjualan_detail')->where('kode_penjualan', $id)->delete();
        
        return redirect()->route('penjualan.index')->with('success', 'Transaksi berhasil dihapus');
    }
}
