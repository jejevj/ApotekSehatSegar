<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Pelanggan;
use Yajra\DataTables\Facades\DataTables;

class PelangganController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:pelanggan.view')->only('index', 'data');
        $this->middleware('permission:pelanggan.create')->only('create', 'store');
        $this->middleware('permission:pelanggan.update')->only('edit', 'update');
        $this->middleware('permission:pelanggan.delete')->only('destroy');
    }

    public function index()
    {
        return view('pelanggan.index');
    }

    public function data()
    {
        $pelanggans = Pelanggan::query();
        return DataTables::of($pelanggans)
            ->addIndexColumn()
            ->addColumn('aksi', function ($pelanggan) {
                $buttons = '';
                if (auth()->user()->hasPermission('pelanggan.update')) {
                    $buttons .= '<a href="' . route('pelanggan.edit', $pelanggan->kode_pelanggan) . '" class="btn btn-success"><i class="material-icons">edit</i></a> ';
                }
                if (auth()->user()->hasPermission('pelanggan.delete')) {
                    $buttons .= '<form action="' . route('pelanggan.destroy', $pelanggan->kode_pelanggan) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Hapus pelanggan ini?\')">' .
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
        return view('pelanggan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'tipe' => 'required|in:umum,khusus',
            'alamat' => 'required',
            'telpon' => 'required',
        ]);

        $pelanggan = Pelanggan::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'pelanggan' => $pelanggan
            ]);
        }

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        return view('pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'telpon' => 'required',
        ]);

        $pelanggan->update($request->all());

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil diupdate');
    }

    public function destroy($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->delete();

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus');
    }
}
