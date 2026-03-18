<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DistributorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:distributor.view')->only('index', 'data');
        $this->middleware('permission:distributor.create')->only('create', 'store');
        $this->middleware('permission:distributor.update')->only('edit', 'update');
        $this->middleware('permission:distributor.delete')->only('destroy');
    }

    public function index()
    {
        return view('distributor.index');
    }

    public function data()
    {
        $distributors = Distributor::query();
        return DataTables::of($distributors)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $buttons = '';
                if (auth()->user()->hasPermission('distributor.update')) {
                    $buttons .= '<a href="' . route('distributor.edit', $row->id) . '" class="btn btn-xs btn-success"><i class="material-icons">edit</i></a> ';
                }
                if (auth()->user()->hasPermission('distributor.delete')) {
                    $buttons .= '<form action="' . route('distributor.destroy', $row->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin ingin menghapus distributor ini?\')">' .
                               csrf_field() .
                               method_field('DELETE') .
                               '<button type="submit" class="btn btn-xs btn-danger"><i class="material-icons">delete</i></button></form>';
                }
                return $buttons;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        return view('distributor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'keterangan' => 'nullable|string',
        ]);

        Distributor::create($request->all());

        return redirect()->route('distributor.index')->with('success', 'Distributor berhasil ditambahkan');
    }

    public function edit($id)
    {
        $distributor = Distributor::findOrFail($id);
        return view('distributor.edit', compact('distributor'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'keterangan' => 'nullable|string',
        ]);

        $distributor = Distributor::findOrFail($id);
        $distributor->update($request->all());

        return redirect()->route('distributor.index')->with('success', 'Distributor berhasil diperbarui');
    }

    public function destroy($id)
    {
        $distributor = Distributor::findOrFail($id);
        $distributor->delete();

        return redirect()->route('distributor.index')->with('success', 'Distributor berhasil dihapus');
    }
}
