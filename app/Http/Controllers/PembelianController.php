<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Distributor;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\PembayaranPembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PembelianController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:pembelian.view')->only('index', 'data', 'show');
        $this->middleware('permission:pembelian.create')->only('create', 'store');
        $this->middleware('permission:pembelian.update')->only('bayar');
        $this->middleware('permission:pembelian.delete')->only('destroy');
    }

    public function index()
    {
        return view('pembelian.index');
    }

    public function data()
    {
        $pembelians = Pembelian::with(['distributor', 'user'])->orderBy('tanggal', 'desc')->orderBy('id', 'desc');

        return DataTables::of($pembelians)
            ->addIndexColumn()
            ->editColumn('tanggal', function ($row) {
                return $row->tanggal ? $row->tanggal->translatedFormat('d F Y') : '-';
            })
            ->addColumn('distributor_nama', function ($row) {
                return $row->distributor?->nama ?? '-';
            })
            ->editColumn('total', function ($row) {
                return 'Rp ' . number_format($row->total, 0, ',', '.');
            })
            ->editColumn('sisa', function ($row) {
                return 'Rp ' . number_format($row->sisa, 0, ',', '.');
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->status === 'lunas') {
                    return '<span class="label bg-green">Lunas</span>';
                }
                return '<span class="label bg-red">Belum Lunas</span>';
            })
            ->addColumn('aksi', function ($row) {
                $buttons = '<a href="' . route('pembelian.show', $row->id) . '" class="btn btn-xs btn-info"><i class="material-icons">visibility</i></a> ';
                if (auth()->user()->hasPermission('pembelian.delete')) {
                    $buttons .= '<form action="' . route('pembelian.destroy', $row->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin ingin menghapus data pembelian ini? Stok barang akan dikurangi kembali.\')">' .
                               csrf_field() .
                               method_field('DELETE') .
                               '<button type="submit" class="btn btn-xs btn-danger"><i class="material-icons">delete</i></button></form>';
                }
                return $buttons;
            })
            ->rawColumns(['status_badge', 'aksi'])
            ->make(true);
    }

    public function create()
    {
        $distributors = Distributor::orderBy('nama')->get();
        $barangs = Barang::orderBy('nama_barang')->get();
        return view('pembelian.create', compact('distributors', 'barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_faktur' => 'required|unique:pembelians,no_faktur',
            'tanggal' => 'required|date',
            'distributor_id' => 'required|exists:distributors,id',
            'items' => 'required|array|min:1',
            'items.*.kode_barcode' => 'required|exists:tb_barang,kode_barcode',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'items.*.jumlah' => 'required|integer|min:1',
            'dibayar' => 'required|numeric|min:0',
            'bukti_nota' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $buktiPath = null;
            if ($request->hasFile('bukti_nota')) {
                $buktiPath = $request->file('bukti_nota')->store('bukti_pembelian', 'public');
            }

            $total = 0;
            foreach ($request->items as $item) {
                $total += ($item['harga_beli'] * $item['jumlah']);
            }

            $dibayar = $request->dibayar;
            $sisa = $total - $dibayar;
            $status = $sisa <= 0 ? 'lunas' : 'belum_lunas';

            $pembelian = Pembelian::create([
                'no_faktur' => $request->no_faktur,
                'tanggal' => $request->tanggal,
                'distributor_id' => $request->distributor_id,
                'total' => $total,
                'dibayar' => $dibayar,
                'sisa' => $sisa > 0 ? $sisa : 0,
                'status' => $status,
                'bukti_nota' => $buktiPath,
                'user_id' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                $barang = Barang::where('kode_barcode', $item['kode_barcode'])->first();
                $subtotal = $item['harga_beli'] * $item['jumlah'];

                PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'kode_barcode' => $item['kode_barcode'],
                    'nama_barang' => $barang->nama_barang,
                    'harga_beli' => $item['harga_beli'],
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $subtotal,
                ]);

                // Update stok dan harga beli barang
                $barang->increment('stok', $item['jumlah']);
                $barang->update(['harga_beli' => $item['harga_beli']]);
            }

            if ($dibayar > 0) {
                PembayaranPembelian::create([
                    'pembelian_id' => $pembelian->id,
                    'tanggal_bayar' => $request->tanggal,
                    'nominal' => $dibayar,
                    'metode_pembayaran' => 'Tunai/Transfer',
                    'keterangan' => 'Pembayaran awal saat faktur dibuat',
                    'user_id' => auth()->id(),
                ]);
            }

            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Data barang masuk berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $pembelian = Pembelian::with(['distributor', 'user', 'details', 'pembayarans.user'])->findOrFail($id);
        return view('pembelian.show', compact('pembelian'));
    }

    public function bayar(Request $request, $id)
    {
        $request->validate([
            'tanggal_bayar' => 'required|date',
            'nominal' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|string',
            'bukti_bayar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        $pembelian = Pembelian::findOrFail($id);

        if ($pembelian->status === 'lunas') {
            return back()->with('error', 'Faktur ini sudah lunas.');
        }

        if ($request->nominal > $pembelian->sisa) {
            return back()->with('error', 'Nominal pembayaran melebihi sisa tagihan.');
        }

        DB::beginTransaction();
        try {
            $buktiPath = null;
            if ($request->hasFile('bukti_bayar')) {
                $buktiPath = $request->file('bukti_bayar')->store('bukti_pembayaran', 'public');
            }

            PembayaranPembelian::create([
                'pembelian_id' => $pembelian->id,
                'tanggal_bayar' => $request->tanggal_bayar,
                'nominal' => $request->nominal,
                'metode_pembayaran' => $request->metode_pembayaran,
                'bukti_bayar' => $buktiPath,
                'keterangan' => $request->keterangan,
                'user_id' => auth()->id(),
            ]);

            $newDibayar = $pembelian->dibayar + $request->nominal;
            $newSisa = $pembelian->total - $newDibayar;
            $status = $newSisa <= 0 ? 'lunas' : 'belum_lunas';

            $pembelian->update([
                'dibayar' => $newDibayar,
                'sisa' => $newSisa > 0 ? $newSisa : 0,
                'status' => $status,
            ]);

            DB::commit();
            return back()->with('success', 'Pembayaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $pembelian = Pembelian::with('details')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Kembalikan stok
            foreach ($pembelian->details as $detail) {
                $barang = Barang::where('kode_barcode', $detail->kode_barcode)->first();
                if ($barang) {
                    $barang->decrement('stok', $detail->jumlah);
                }
            }

            if ($pembelian->bukti_nota) {
                Storage::disk('public')->delete($pembelian->bukti_nota);
            }

            foreach ($pembelian->pembayarans as $bayar) {
                if ($bayar->bukti_bayar) {
                    Storage::disk('public')->delete($bayar->bukti_bayar);
                }
            }

            $pembelian->delete();

            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Data pembelian berhasil dihapus dan stok telah disesuaikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
