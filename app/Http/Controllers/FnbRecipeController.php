<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\FnbIngredient;
use App\Models\FnbRecipe;
use App\Models\FnbRecipeItem;
use App\Models\Unit;
use App\Services\FnbHppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FnbRecipeController extends Controller
{
    public function __construct(private FnbHppService $hppService)
    {
        $this->middleware('permission:recipes.view')->only('index', 'data');
        $this->middleware('permission:recipes.create')->only('create', 'store');
        $this->middleware('permission:recipes.update')->only('edit', 'update');
        $this->middleware('permission:recipes.delete')->only('destroy');
    }

    public function index()
    {
        return view('fnb.recipes.index');
    }

    public function data()
    {
        $recipes = FnbRecipe::with('menu');

        return DataTables::of($recipes)
            ->addIndexColumn()
            ->addColumn('menu_nama', fn($r) => $r->menu?->nama_barang ?? '-')
            ->addColumn('hpp_fmt', fn($r) => 'Rp ' . number_format($r->hpp_per_porsi, 0, ',', '.'))
            ->addColumn('hpp_status', function ($r) {
                if ($r->hpp_outdated) {
                    return '<span class="badge bg-orange text-white">Perlu Update</span>';
                }
                return '<span class="badge bg-green text-white">Terkini</span>';
            })
            ->addColumn('aksi', function ($r) {
                $buttons = '';
                if (auth()->user()->hasPermission('recipes.update')) {
                    $buttons .= '<a href="' . route('fnb.recipes.edit', $r->id) . '" class="btn btn-success btn-sm"><i class="material-icons">edit</i></a> ';
                }
                if (auth()->user()->hasPermission('recipes.delete')) {
                    $buttons .= '<form action="' . route('fnb.recipes.destroy', $r->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Hapus resep ini?\')">' .
                        csrf_field() . method_field('DELETE') .
                        '<button type="submit" class="btn btn-danger btn-sm"><i class="material-icons">delete</i></button></form>';
                }
                return $buttons;
            })
            ->rawColumns(['hpp_status', 'aksi'])
            ->make(true);
    }

    public function create()
    {
        $menus = Barang::where('is_menu', true)->orderBy('nama_barang')->get();
        $ingredients = FnbIngredient::with('unit')->orderBy('nama')->get();
        $units = Unit::orderBy('nama')->get();
        return view('fnb.recipes.form', compact('menus', 'ingredients', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_id'               => 'required|string',
            'nama_resep'            => 'required|string|max:150',
            'porsi'                 => 'required|integer|min:1',
            'keterangan'            => 'nullable|string',
            'recipe_items'          => 'required|array|min:1',
            'recipe_items.*.ingredient_id' => 'required|exists:fnb_ingredients,id',
            'recipe_items.*.qty'    => 'required|numeric|min:0.001',
            'recipe_items.*.unit_id' => 'required|exists:units,id',
        ]);

        // Pastikan menu_id milik toko yang sama
        $menu = Barang::where('kode_barcode', $validated['menu_id'])->first();
        if (!$menu) {
            return back()->withErrors(['menu_id' => 'Menu tidak ditemukan.'])->withInput();
        }

        DB::transaction(function () use ($validated) {
            $recipe = FnbRecipe::create([
                'menu_id'     => $validated['menu_id'],
                'nama_resep'  => $validated['nama_resep'],
                'porsi'       => $validated['porsi'],
                'keterangan'  => $validated['keterangan'] ?? null,
            ]);

            foreach ($validated['recipe_items'] as $item) {
                FnbRecipeItem::create([
                    'recipe_id'     => $recipe->id,
                    'ingredient_id' => $item['ingredient_id'],
                    'qty'           => $item['qty'],
                    'unit_id'       => $item['unit_id'],
                    'keterangan'    => $item['keterangan'] ?? null,
                ]);
            }

            $hpp = $this->hppService->calculateHpp($recipe->fresh(['items.ingredient']));
            $recipe->update(['hpp_per_porsi' => $hpp, 'hpp_outdated' => false]);
        });

        return redirect()->route('fnb.recipes.index')->with('success', 'Resep berhasil ditambahkan.');
    }

    public function edit(FnbRecipe $recipe)
    {
        $recipe->load('items.ingredient', 'items.unit');
        $menus = Barang::where('is_menu', true)->orderBy('nama_barang')->get();
        $ingredients = FnbIngredient::with('unit')->orderBy('nama')->get();
        $units = Unit::orderBy('nama')->get();
        return view('fnb.recipes.form', compact('recipe', 'menus', 'ingredients', 'units'));
    }

    public function update(Request $request, FnbRecipe $recipe)
    {
        $validated = $request->validate([
            'menu_id'               => 'required|string',
            'nama_resep'            => 'required|string|max:150',
            'porsi'                 => 'required|integer|min:1',
            'keterangan'            => 'nullable|string',
            'recipe_items'          => 'required|array|min:1',
            'recipe_items.*.ingredient_id' => 'required|exists:fnb_ingredients,id',
            'recipe_items.*.qty'    => 'required|numeric|min:0.001',
            'recipe_items.*.unit_id' => 'required|exists:units,id',
        ]);

        $menu = Barang::where('kode_barcode', $validated['menu_id'])->first();
        if (!$menu) {
            return back()->withErrors(['menu_id' => 'Menu tidak ditemukan.'])->withInput();
        }

        DB::transaction(function () use ($validated, $recipe) {
            $recipe->update([
                'menu_id'    => $validated['menu_id'],
                'nama_resep' => $validated['nama_resep'],
                'porsi'      => $validated['porsi'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // Hapus items lama, buat ulang
            $recipe->items()->delete();
            foreach ($validated['recipe_items'] as $item) {
                FnbRecipeItem::create([
                    'recipe_id'     => $recipe->id,
                    'ingredient_id' => $item['ingredient_id'],
                    'qty'           => $item['qty'],
                    'unit_id'       => $item['unit_id'],
                    'keterangan'    => $item['keterangan'] ?? null,
                ]);
            }

            $hpp = $this->hppService->calculateHpp($recipe->fresh(['items.ingredient']));
            $recipe->update(['hpp_per_porsi' => $hpp, 'hpp_outdated' => false]);
        });

        return redirect()->route('fnb.recipes.index')->with('success', 'Resep berhasil diperbarui.');
    }

    public function destroy(FnbRecipe $recipe)
    {
        $recipe->delete();
        return redirect()->route('fnb.recipes.index')->with('success', 'Resep berhasil dihapus.');
    }
}
