<?php

namespace App\Http\Controllers;

use App\Models\Rak;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RakController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:rak.view')->only('index', 'data');
        $this->middleware('permission:rak.create')->only('create', 'store');
        $this->middleware('permission:rak.update')->only('edit', 'update');
        $this->middleware('permission:rak.delete')->only('destroy');
    }

    public function index()
    {
        return view('rak.index');
    }

    public function data()
    {
        $raks = Rak::query();
        return DataTables::of($raks)
            ->addIndexColumn()
            ->addColumn('aksi', function ($rak) {
                $buttons = '';
                if (auth()->user()->hasPermission('rak.update')) {
                    $buttons .= '<a href="' . route('rak.edit', $rak->id) . '" class="btn btn-success"><i class="material-icons">edit</i></a> ';
                }
                if (auth()->user()->hasPermission('rak.delete')) {
                    $buttons .= '<form action="' . route('rak.destroy', $rak->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Hapus rak ini?\')">' .
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
        return view('rak.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_rak' => 'required|unique:tb_rak,nama_rak',
        ]);

        Rak::create([
            'nama_rak' => $request->nama_rak,
        ]);

        return redirect()->route('rak.index')->with('success', 'Rak berhasil ditambahkan');
    }

    public function edit($id)
    {
        $rak = Rak::findOrFail($id);
        return view('rak.edit', compact('rak'));
    }

    public function update(Request $request, $id)
    {
        $rak = Rak::findOrFail($id);

        $request->validate([
            'nama_rak' => 'required|unique:tb_rak,nama_rak,' . $rak->id,
        ]);

        $rak->update([
            'nama_rak' => $request->nama_rak,
        ]);

        return redirect()->route('rak.index')->with('success', 'Rak berhasil diupdate');
    }

    public function destroy($id)
    {
        $rak = Rak::findOrFail($id);
        $rak->delete();
        return redirect()->route('rak.index')->with('success', 'Rak berhasil dihapus');
    }
}
