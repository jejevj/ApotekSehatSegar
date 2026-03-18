<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockOpnameController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:opname.view')->only('index', 'data', 'show');
        $this->middleware('permission:opname.create')->only('create', 'store', 'addItem');
        $this->middleware('permission:opname.update')->only('edit', 'update', 'updateItem', 'removeItem', 'finish', 'cancel');
        $this->middleware('permission:opname.approve')->only('approve', 'reject');
        $this->middleware('permission:opname.delete')->only('destroy');
    }

    public function index()
    {
        return view('opname.index');
    }

    public function data()
    {
        $opnames = StockOpname::with(['user', 'approvedBy'])->orderBy('id', 'desc');

        return DataTables::of($opnames)
            ->addIndexColumn()
            ->editColumn('tanggal', function ($row) {
                return $row->tanggal ? $row->tanggal->translatedFormat('d F Y H:i') : '-';
            })
            ->addColumn('user_nama', function ($row) {
                return $row->user?->nama ?? '-';
            })
            ->addColumn('approved_by_nama', function ($row) {
                return $row->approvedBy?->nama ?? '-';
            })
            ->addColumn('nilai_rugi', function ($row) {
                return 'Rp ' . number_format($row->total_nilai_rugi, 0, ',', '.');
            })
            ->addColumn('nilai_lebih', function ($row) {
                return 'Rp ' . number_format($row->total_nilai_lebih, 0, ',', '.');
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->status === 'draft') return '<span class="label bg-amber">Draft</span>';
                if ($row->status === 'menunggu_approve') return '<span class="label bg-orange">Menunggu Approve</span>';
                if ($row->status === 'selesai') return '<span class="label bg-green">Selesai</span>';
                if ($row->status === 'dibatalkan') return '<span class="label bg-red">Dibatalkan</span>';
                return '<span class="label bg-grey">-</span>';
            })
            ->addColumn('aksi', function ($row) {
                $btn = '';
                if ($row->status === 'draft' && auth()->user()->hasPermission('opname.update')) {
                    $btn .= '<a href="' . route('opname.edit', $row->id) . '" class="btn btn-xs btn-success"><i class="material-icons">edit</i></a> ';
                } else {
                    $btn .= '<a href="' . route('opname.show', $row->id) . '" class="btn btn-xs btn-info"><i class="material-icons">visibility</i></a> ';
                }

                if ($row->status === 'menunggu_approve' && auth()->user()->hasPermission('opname.approve')) {
                    $btn .= '<form action="' . route('opname.approve', $row->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Setujui opname ini dan sesuaikan stok?\')">'
                        . csrf_field()
                        . '<button type="submit" class="btn btn-xs btn-primary"><i class="material-icons">check_circle</i></button>'
                        . '</form> ';
                }

                if (auth()->user()->hasPermission('opname.delete')) {
                    $btn .= '<form action="' . route('opname.destroy', $row->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Hapus opname ini?\')">'
                        . csrf_field()
                        . method_field('DELETE')
                        . '<button type="submit" class="btn btn-xs btn-danger"><i class="material-icons">delete</i></button>'
                        . '</form>';
                }
                return $btn;
            })
            ->rawColumns(['status_badge', 'aksi'])
            ->make(true);
    }

    public function create()
    {
        return view('opname.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        $kode = 'OP-' . rand(1000000000, 9999999999);
        $opname = StockOpname::create([
            'kode_opname' => $kode,
            'tanggal' => Carbon::parse($request->tanggal)->format('Y-m-d H:i:s'),
            'catatan' => $request->catatan,
            'status' => 'draft',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('opname.edit', $opname->id)->with('success', 'Opname dibuat, silakan input item');
    }

    public function show($id)
    {
        $opname = StockOpname::with(['items', 'user', 'approvedBy'])->findOrFail($id);
        return view('opname.show', compact('opname'));
    }

    public function edit($id)
    {
        $opname = StockOpname::with('items')->findOrFail($id);
        if ($opname->status !== 'draft') {
            return redirect()->route('opname.show', $opname->id)->with('error', 'Opname sudah diproses');
        }
        return view('opname.edit', compact('opname'));
    }

    public function update(Request $request, $id)
    {
        $opname = StockOpname::findOrFail($id);
        if ($opname->status !== 'draft') {
            return redirect()->route('opname.index')->with('error', 'Opname tidak bisa diubah karena sudah diproses');
        }

        $request->validate([
            'tanggal' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        $opname->update([
            'tanggal' => Carbon::parse($request->tanggal)->format('Y-m-d H:i:s'),
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('opname.edit', $opname->id)->with('success', 'Opname diperbarui');
    }

    public function addItem(Request $request, $id)
    {
        $opname = StockOpname::findOrFail($id);
        if ($opname->status !== 'draft') {
            return redirect()->route('opname.index')->with('error', 'Opname tidak bisa diubah karena sudah diproses');
        }

        $request->validate([
            'kode_barcode' => 'required|string',
            'stok_fisik' => 'required|integer|min:0',
        ]);

        $barang = Barang::where('kode_barcode', $request->kode_barcode)->first();
        if (!$barang) {
            return redirect()->route('opname.edit', $opname->id)->with('error', 'Barang tidak ditemukan');
        }

        $existing = StockOpnameItem::where('stock_opname_id', $opname->id)
            ->where('kode_barcode', $barang->kode_barcode)
            ->first();

        $stokFisik = (int) $request->stok_fisik;
        $hargaBeli = (int) $barang->harga_beli;

        if ($existing) {
            $stokSistem = $existing->stok_sistem;
            $selisih = $stokFisik - $stokSistem;
            $existing->update([
                'stok_fisik' => $stokFisik,
                'selisih' => $selisih,
                'harga_beli' => $hargaBeli,
                'nilai_selisih' => $selisih * $hargaBeli,
            ]);
        } else {
            $stokSistem = (int) $barang->stok;
            $selisih = $stokFisik - $stokSistem;
            StockOpnameItem::create([
                'stock_opname_id' => $opname->id,
                'kode_barcode' => $barang->kode_barcode,
                'nama_barang' => $barang->nama_barang,
                'harga_beli' => $hargaBeli,
                'stok_sistem' => $stokSistem,
                'stok_fisik' => $stokFisik,
                'selisih' => $selisih,
                'nilai_selisih' => $selisih * $hargaBeli,
            ]);
        }

        return redirect()->route('opname.edit', $opname->id)->with('success', 'Item opname tersimpan');
    }

    public function updateItem(Request $request, $opnameId, $itemId)
    {
        $opname = StockOpname::findOrFail($opnameId);
        if ($opname->status !== 'draft') {
            return redirect()->route('opname.index')->with('error', 'Opname tidak bisa diubah karena sudah diproses');
        }

        $item = StockOpnameItem::where('stock_opname_id', $opname->id)->findOrFail($itemId);
        $request->validate([
            'stok_fisik' => 'required|integer|min:0',
        ]);

        $stokFisik = (int) $request->stok_fisik;
        $stokSistem = (int) $item->stok_sistem;
        $selisih = $stokFisik - $stokSistem;
        $hargaBeli = (int) $item->harga_beli;

        $item->update([
            'stok_fisik' => $stokFisik,
            'selisih' => $selisih,
            'nilai_selisih' => $selisih * $hargaBeli,
        ]);

        return redirect()->route('opname.edit', $opname->id)->with('success', 'Item diperbarui');
    }

    public function removeItem($opnameId, $itemId)
    {
        $opname = StockOpname::findOrFail($opnameId);
        if ($opname->status !== 'draft') {
            return redirect()->route('opname.index')->with('error', 'Opname tidak bisa diubah karena sudah diproses');
        }
        $item = StockOpnameItem::where('stock_opname_id', $opname->id)->findOrFail($itemId);
        $item->delete();
        return redirect()->route('opname.edit', $opname->id)->with('success', 'Item dihapus');
    }

    public function finish($id)
    {
        $opname = StockOpname::with('items')->findOrFail($id);
        if ($opname->status !== 'draft') {
            return redirect()->route('opname.index')->with('error', 'Opname sudah diproses');
        }
        if ($opname->items->count() === 0) {
            return redirect()->route('opname.edit', $opname->id)->with('error', 'Item opname masih kosong');
        }

        // Recalculate based on current items to fix any missing values
        foreach ($opname->items as $item) {
            $barang = Barang::where('kode_barcode', $item->kode_barcode)->first();
            if ($barang) {
                $item->update([
                    'harga_beli' => $barang->harga_beli,
                    'nilai_selisih' => $item->selisih * $barang->harga_beli,
                ]);
            }
        }

        $totalRugi = $opname->items->where('selisih', '<', 0)->sum('nilai_selisih');
        $totalLebih = $opname->items->where('selisih', '>', 0)->sum('nilai_selisih');

        $opname->update([
            'status' => 'menunggu_approve',
            'finished_at' => Carbon::now(),
            'total_nilai_rugi' => abs($totalRugi),
            'total_nilai_lebih' => $totalLebih,
        ]);

        return redirect()->route('opname.show', $opname->id)->with('success', 'Opname diajukan untuk verifikasi');
    }

    public function approve($id)
    {
        $opname = StockOpname::with('items')->findOrFail($id);
        if ($opname->status !== 'menunggu_approve') {
            return redirect()->route('opname.index')->with('error', 'Opname tidak dalam status menunggu verifikasi');
        }

        DB::transaction(function () use ($opname) {
            foreach ($opname->items as $item) {
                Barang::where('kode_barcode', $item->kode_barcode)->update([
                    'stok' => $item->stok_fisik,
                ]);
            }

            $opname->update([
                'status' => 'selesai',
                'approved_by' => auth()->id(),
                'approved_at' => Carbon::now(),
            ]);
        });

        return redirect()->route('opname.show', $opname->id)->with('success', 'Opname disetujui dan stok telah diperbarui');
    }

    public function reject($id)
    {
        $opname = StockOpname::findOrFail($id);
        if ($opname->status !== 'menunggu_approve') {
            return redirect()->route('opname.index')->with('error', 'Opname tidak dalam status menunggu verifikasi');
        }

        $opname->update([
            'status' => 'draft', // dikembalikan ke draft agar bisa diperbaiki
        ]);

        return redirect()->route('opname.show', $opname->id)->with('success', 'Opname dikembalikan ke draft untuk diperbaiki');
    }

    public function dataItems($id)
    {
        $items = StockOpnameItem::where('stock_opname_id', $id)->orderBy('nama_barang', 'asc');

        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('harga_beli_rp', function ($row) {
                return 'Rp ' . number_format($row->harga_beli, 0, ',', '.');
            })
            ->addColumn('nilai_selisih_rp', function ($row) {
                $class = $row->nilai_selisih < 0 ? 'text-danger' : ($row->nilai_selisih > 0 ? 'text-success' : '');
                return '<span class="' . $class . '">Rp ' . number_format($row->nilai_selisih, 0, ',', '.') . '</span>';
            })
            ->rawColumns(['nilai_selisih_rp'])
            ->make(true);
    }

    public function cancel($id)
    {
        $opname = StockOpname::findOrFail($id);
        if ($opname->status !== 'draft') {
            return redirect()->route('opname.index')->with('error', 'Opname sudah diproses');
        }
        $opname->update([
            'status' => 'dibatalkan',
        ]);
        return redirect()->route('opname.index')->with('success', 'Opname dibatalkan');
    }

    public function destroy($id)
    {
        $opname = StockOpname::findOrFail($id);
        $opname->delete();
        return redirect()->route('opname.index')->with('success', 'Opname berhasil dihapus');
    }
}

