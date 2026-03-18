<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LokasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:lokasi.view')->only('index', 'data');
        $this->middleware('permission:lokasi.create')->only('create', 'store');
        $this->middleware('permission:lokasi.update')->only('edit', 'update');
        $this->middleware('permission:lokasi.delete')->only('destroy');
    }

    public function index()
    {
        return view('lokasi.index');
    }

    public function data()
    {
        $lokasis = Lokasi::query();
        return DataTables::of($lokasis)
            ->addIndexColumn()
            ->addColumn('aksi', function ($lokasi) {
                $buttons = '';
                if (auth()->user()->hasPermission('lokasi.update')) {
                    $buttons .= '<a href="' . route('lokasi.edit', $lokasi->id) . '" class="btn btn-success"><i class="material-icons">edit</i></a> ';
                }
                if (auth()->user()->hasPermission('lokasi.delete')) {
                    $buttons .= '<form action="' . route('lokasi.destroy', $lokasi->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Hapus lokasi ini?\')">' .
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
        return view('lokasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|unique:tb_lokasi,nama_lokasi',
        ]);

        Lokasi::create([
            'nama_lokasi' => $request->nama_lokasi,
        ]);

        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil ditambahkan');
    }

    public function edit($id)
    {
        $lokasi = Lokasi::findOrFail($id);
        return view('lokasi.edit', compact('lokasi'));
    }

    public function update(Request $request, $id)
    {
        $lokasi = Lokasi::findOrFail($id);

        $request->validate([
            'nama_lokasi' => 'required|unique:tb_lokasi,nama_lokasi,' . $lokasi->id,
        ]);

        $lokasi->update([
            'nama_lokasi' => $request->nama_lokasi,
        ]);

        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil diupdate');
    }

    public function destroy($id)
    {
        $lokasi = Lokasi::findOrFail($id);
        $lokasi->delete();
        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil dihapus');
    }
}
