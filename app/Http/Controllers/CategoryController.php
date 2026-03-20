<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:category.view')->only('index', 'data');
        $this->middleware('permission:category.create')->only('create', 'store');
        $this->middleware('permission:category.update')->only('edit', 'update');
        $this->middleware('permission:category.delete')->only('destroy');
    }

    public function index()
    {
        return view('category.index');
    }

    public function data()
    {
        $categories = Category::query();
        return DataTables::of($categories)
            ->addIndexColumn()
            ->addColumn('aksi', function ($category) {
                $buttons = '';
                if (auth()->user()->hasPermission('category.update')) {
                    $buttons .= '<a href="' . route('category.edit', $category->id) . '" class="btn btn-success"><i class="material-icons">edit</i></a> ';
                }
                if (auth()->user()->hasPermission('category.delete') && $category->id != 1) {
                    $buttons .= '<form action="' . route('category.destroy', $category->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Hapus kategori ini?\')">' .
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
        return view('category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|unique:categories,nama_kategori',
        ]);

        Category::create([
            'nama_kategori' => $request->nama_kategori,
            'slug' => Str::slug($request->nama_kategori),
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('category.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('category.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|unique:categories,nama_kategori,' . $category->id,
        ]);

        $category->update([
            'nama_kategori' => $request->nama_kategori,
            'slug' => Str::slug($request->nama_kategori),
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('category.index')->with('success', 'Kategori berhasil diupdate');
    }

    public function destroy($id)
    {
        if ($id == 1) {
            return redirect()->route('category.index')->with('error', 'Kategori default tidak bisa dihapus');
        }

        $category = Category::findOrFail($id);
        
        // Move barangs to default category
        $category->barangs()->update(['category_id' => 1]);
        
        $category->delete();
        return redirect()->route('category.index')->with('success', 'Kategori berhasil dihapus');
    }
}
