<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:barang.view')->only('index', 'data', 'productsData');
        $this->middleware('permission:barang.create')->only('create', 'store');
        $this->middleware('permission:barang.update')->only('edit', 'update');
        $this->middleware('permission:barang.delete')->only('destroy');
    }

    public function index()
    {
        return view('barang.index');
    }

    public function data()
    {
        $barangs = Barang::query();
        return DataTables::of($barangs)
            ->addIndexColumn()
            ->addColumn('aksi', function ($barang) {
                $buttons = '';
                if (auth()->user()->hasPermission('barang.update')) {
                    $buttons .= '<a href="' . route('barang.edit', $barang->kode_barcode) . '" class="btn btn-success"><i class="material-icons">edit</i></a> ';
                }
                if (auth()->user()->hasPermission('barang.delete')) {
                    $buttons .= '<form action="' . route('barang.destroy', $barang->kode_barcode) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Apakah Anda Yakin Akan Mengahapus Data ini???\')">' .
                               csrf_field() .
                               method_field('DELETE') .
                               '<button type="submit" class="btn btn-danger"><i class="material-icons">delete</i></button></form>';
                }
                return $buttons;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barcode' => 'required|unique:tb_barang',
            'nama_barang' => 'required',
            'satuan' => 'required',
            'harga_beli' => 'required|numeric',
            'stok' => 'required|numeric',
            'harga_jual' => 'required|numeric',
        ]);

        $profit = $request->harga_jual - $request->harga_beli;

        Barang::create(array_merge($request->all(), ['profit' => $profit]));

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $request->validate([
            'nama_barang' => 'required',
            'satuan' => 'required',
            'harga_beli' => 'required|numeric',
            'stok' => 'required|numeric',
            'harga_jual' => 'required|numeric',
        ]);

        $profit = $request->harga_jual - $request->harga_beli;

        $barang->update(array_merge($request->all(), ['profit' => $profit]));

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diupdate');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus');
    }

    public function productsData()
    {
        $barangs = Barang::query();
        return DataTables::of($barangs)
            ->addIndexColumn()
            ->addColumn('harga_jual_formatted', function ($barang) {
                return 'Rp. ' . number_format($barang->harga_jual, 0, ',', '.');
            })
            ->addColumn('action', function ($barang) {
                $cleanBarcode = htmlspecialchars($barang->kode_barcode, ENT_QUOTES, 'UTF-8');
                return '<div class="input-group">' .
                       '<input type="number" class="form-control" value="1" min="1" max="' . $barang->stok . '" id="jumlah-' . $cleanBarcode . '">' .
                       '<span class="input-group-btn"><button type="button" class="btn btn-primary btn-xs select-product-from-modal" data-barcode="' . $cleanBarcode . '">Pilih</button></span>' .
                       '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
