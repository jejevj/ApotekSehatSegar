<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

use App\Models\Menu;
use App\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class MenuController extends Controller
{
    public function index()
    {
        return view('menu.index');
    }

    public function data()
    {
        $menus = Menu::query()->with('parent');
        return DataTables::of($menus)
            ->addIndexColumn()
            ->addColumn('parent_name', function ($menu) {
                return $menu->parent ? $menu->parent->name : '-';
            })
            ->addColumn('aksi', function ($menu) {
                return '<a href="' . route('menu.edit', $menu->id) . '" class="btn btn-success"><i class="material-icons">edit</i></a> ' .
                       '<form action="' . route('menu.destroy', $menu->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Hapus menu ini?\')">' .
                       csrf_field() .
                       method_field('DELETE') .
                       '<button type="submit" class="btn btn-danger"><i class="material-icons">delete</i></button></form>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        $parentMenus = Menu::whereNull('parent_id')->get();
        return view('menu.create', compact('roles', 'parentMenus', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'order' => 'required|numeric',
            'roles' => 'required|array'
        ]);

        $menu = Menu::create($request->all());
        $menu->roles()->sync($request->roles);

        return redirect()->route('menu.index')->with('success', 'Menu berhasil ditambahkan');
    }

    public function edit($id)
    {
        $menu = Menu::with('roles')->findOrFail($id);
        $roles = Role::all();
        $permissions = Permission::all();
        $parentMenus = Menu::whereNull('parent_id')->where('id', '!=', $id)->get();
        $menuRoles = $menu->roles->pluck('id')->toArray();
        return view('menu.edit', compact('menu', 'roles', 'parentMenus', 'menuRoles', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $request->validate([
            'name' => 'required',
            'order' => 'required|numeric',
            'roles' => 'required|array'
        ]);

        $menu->update($request->all());
        $menu->roles()->sync($request->roles);

        return redirect()->route('menu.index')->with('success', 'Menu berhasil diupdate');
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        return redirect()->route('menu.index')->with('success', 'Menu berhasil dihapus');
    }
}
