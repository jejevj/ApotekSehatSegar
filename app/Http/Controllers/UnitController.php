<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:unit.view')->only('index', 'data');
        $this->middleware('permission:unit.create')->only('create', 'store');
        $this->middleware('permission:unit.update')->only('edit', 'update');
        $this->middleware('permission:unit.delete')->only('destroy');
    }

    public function index()
    {
        return view('unit.index');
    }

    public function data()
    {
        $units = Unit::query();
        return DataTables::of($units)
            ->addIndexColumn()
            ->addColumn('aksi', function ($unit) {
                $buttons = '';
                if (auth()->user()->hasPermission('unit.update')) {
                    $buttons .= '<a href="' . route('unit.edit', $unit->id) . '" class="btn btn-success"><i class="material-icons">edit</i></a> ';
                }
                if (auth()->user()->hasPermission('unit.delete')) {
                    $buttons .= '<form action="' . route('unit.destroy', $unit->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Hapus satuan ini?\')">' .
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
        return view('unit.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|unique:units,nama',
        ]);

        $unit = Unit::create([
            'nama' => $request->nama,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'unit' => $unit
            ]);
        }

        return redirect()->route('unit.index')->with('success', 'Satuan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        return view('unit.edit', compact('unit'));
    }

    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);

        $request->validate([
            'nama' => 'required|unique:units,nama,' . $unit->id,
        ]);

        $unit->update([
            'nama' => $request->nama,
        ]);

        return redirect()->route('unit.index')->with('success', 'Satuan berhasil diupdate');
    }

    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();
        return redirect()->route('unit.index')->with('success', 'Satuan berhasil dihapus');
    }
}
