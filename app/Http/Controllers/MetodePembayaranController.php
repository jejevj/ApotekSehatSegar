<?php

namespace App\Http\Controllers;

use App\Models\MetodePembayaran;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MetodePembayaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:metode_pembayaran.view')->only('index', 'data');
        $this->middleware('permission:metode_pembayaran.create')->only('create', 'store');
        $this->middleware('permission:metode_pembayaran.update')->only('edit', 'update');
        $this->middleware('permission:metode_pembayaran.delete')->only('destroy');
    }

    public function index()
    {
        return view('metode_pembayaran.index');
    }

    public function data()
    {
        $items = MetodePembayaran::query();

        return DataTables::of($items)
            ->addIndexColumn()
            ->editColumn('is_aktif', function ($row) {
                return $row->is_aktif ? 'Aktif' : 'Nonaktif';
            })
            ->addColumn('aksi', function ($row) {
                $buttons = '';
                if (auth()->user()->hasPermission('metode_pembayaran.update')) {
                    $buttons .= '<a href="' . route('metode_pembayaran.edit', $row->id) . '" class="btn btn-success"><i class="material-icons">edit</i></a> ';
                }
                if (auth()->user()->hasPermission('metode_pembayaran.delete')) {
                    $buttons .= '<form action="' . route('metode_pembayaran.destroy', $row->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Hapus metode pembayaran ini?\')">'
                        . csrf_field()
                        . method_field('DELETE')
                        . '<button type="submit" class="btn btn-danger"><i class="material-icons">delete</i></button>'
                        . '</form>';
                }
                return $buttons;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        return view('metode_pembayaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:191',
            'kode' => 'required|string|max:50|unique:metode_pembayarans,kode',
            'tipe' => 'required|in:tunai,transfer',
            'nama_bank' => 'nullable|string|max:191',
            'no_rekening' => 'nullable|string|max:191',
            'is_aktif' => 'nullable|boolean',
        ]);

        $data = $request->only([
            'nama',
            'kode',
            'tipe',
            'nama_bank',
            'no_rekening',
        ]);
        $data['is_aktif'] = $request->boolean('is_aktif', true);

        MetodePembayaran::create($data);

        return redirect()->route('metode_pembayaran.index')->with('success', 'Metode pembayaran berhasil ditambahkan');
    }

    public function edit(MetodePembayaran $metode_pembayaran)
    {
        return view('metode_pembayaran.edit', compact('metode_pembayaran'));
    }

    public function update(Request $request, MetodePembayaran $metode_pembayaran)
    {
        $request->validate([
            'nama' => 'required|string|max:191',
            'kode' => 'required|string|max:50|unique:metode_pembayarans,kode,' . $metode_pembayaran->id,
            'tipe' => 'required|in:tunai,transfer',
            'nama_bank' => 'nullable|string|max:191',
            'no_rekening' => 'nullable|string|max:191',
            'is_aktif' => 'nullable|boolean',
        ]);

        $data = $request->only([
            'nama',
            'kode',
            'tipe',
            'nama_bank',
            'no_rekening',
        ]);
        $data['is_aktif'] = $request->boolean('is_aktif', true);

        $metode_pembayaran->update($data);

        return redirect()->route('metode_pembayaran.index')->with('success', 'Metode pembayaran berhasil diupdate');
    }

    public function destroy(MetodePembayaran $metode_pembayaran)
    {
        $metode_pembayaran->delete();

        return redirect()->route('metode_pembayaran.index')->with('success', 'Metode pembayaran berhasil dihapus');
    }
}

