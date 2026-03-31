<?php

namespace App\Http\Controllers;

use App\Models\FnbIngredient;
use App\Models\FnbRecipe;
use App\Models\Unit;
use App\Services\FnbHppService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FnbIngredientController extends Controller
{
    public function __construct(private FnbHppService $hppService)
    {
        $this->middleware('permission:ingredients.view')->only('index', 'data', 'search');
        $this->middleware('permission:ingredients.create')->only('create', 'store');
        $this->middleware('permission:ingredients.update')->only('edit', 'update');
        $this->middleware('permission:ingredients.delete')->only('destroy');
    }

    public function index()
    {
        return view('fnb.ingredients.index');
    }

    public function data()
    {
        $ingredients = FnbIngredient::with('unit');

        return DataTables::of($ingredients)
            ->addIndexColumn()
            ->addColumn('unit_nama', fn($i) => $i->unit?->nama ?? '-')
            ->addColumn('harga_beli_fmt', fn($i) => 'Rp ' . number_format($i->harga_beli, 0, ',', '.'))
            ->addColumn('stok_fmt', fn($i) => number_format($i->stok, 3, ',', '.'))
            ->addColumn('stok_status', function ($i) {
                if ($i->isLowStock()) {
                    return '<span class="badge bg-red text-white"><i class="material-icons" style="font-size:14px;vertical-align:middle;">warning</i> Stok Rendah</span>';
                }
                return '<span class="badge bg-green text-white">Normal</span>';
            })
            ->addColumn('aksi', function ($i) {
                $buttons = '';
                if (auth()->user()->hasPermission('ingredients.update')) {
                    $buttons .= '<a href="' . route('fnb.ingredients.edit', $i->id) . '" class="btn btn-success btn-sm"><i class="material-icons">edit</i></a> ';
                }
                if (auth()->user()->hasPermission('ingredients.delete')) {
                    $buttons .= '<form action="' . route('fnb.ingredients.destroy', $i->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Hapus bahan baku ini?\')">' .
                        csrf_field() . method_field('DELETE') .
                        '<button type="submit" class="btn btn-danger btn-sm"><i class="material-icons">delete</i></button></form>';
                }
                return $buttons;
            })
            ->rawColumns(['stok_status', 'aksi'])
            ->make(true);
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $ingredients = FnbIngredient::with('unit')
            ->where('nama', 'like', "%{$q}%")
            ->limit(20)
            ->get()
            ->map(fn($i) => [
                'id'   => $i->id,
                'text' => $i->nama . ' (' . ($i->unit?->nama ?? '-') . ')',
                'harga_beli' => $i->harga_beli,
                'unit_id' => $i->unit_id,
                'unit_nama' => $i->unit?->nama ?? '-',
            ]);

        return response()->json(['results' => $ingredients]);
    }

    public function create()
    {
        $units = Unit::orderBy('nama')->get();
        return view('fnb.ingredients.create', compact('units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:150',
            'unit_id'       => 'required|exists:units,id',
            'harga_beli'    => 'required|integer|min:0',
            'stok'          => 'required|numeric|min:0',
            'stok_minimum'  => 'required|numeric|min:0',
            'keterangan'    => 'nullable|string',
        ]);

        FnbIngredient::create($validated);

        return redirect()->route('fnb.ingredients.index')->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function edit(FnbIngredient $ingredient)
    {
        $units = Unit::orderBy('nama')->get();
        return view('fnb.ingredients.edit', compact('ingredient', 'units'));
    }

    public function update(Request $request, FnbIngredient $ingredient)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:150',
            'unit_id'       => 'required|exists:units,id',
            'harga_beli'    => 'required|integer|min:0',
            'stok'          => 'required|numeric|min:0',
            'stok_minimum'  => 'required|numeric|min:0',
            'keterangan'    => 'nullable|string',
        ]);

        $hargaBeliLama = $ingredient->harga_beli;
        $ingredient->update($validated);

        // Recalculate HPP jika harga beli berubah
        if ((int) $validated['harga_beli'] !== (int) $hargaBeliLama) {
            $this->hppService->recalculateForIngredient($ingredient->id);
        }

        return redirect()->route('fnb.ingredients.index')->with('success', 'Bahan baku berhasil diperbarui.');
    }

    public function destroy(FnbIngredient $ingredient)
    {
        // Cek apakah masih dipakai resep
        $usedInRecipes = $ingredient->recipeItems()
            ->with('recipe')
            ->get()
            ->pluck('recipe.nama_resep')
            ->filter()
            ->unique()
            ->values();

        if ($usedInRecipes->isNotEmpty()) {
            $names = $usedInRecipes->implode(', ');
            return back()->with('error', "Bahan baku tidak dapat dihapus karena masih digunakan dalam resep: {$names}.");
        }

        $ingredient->delete();
        return redirect()->route('fnb.ingredients.index')->with('success', 'Bahan baku berhasil dihapus.');
    }
}
