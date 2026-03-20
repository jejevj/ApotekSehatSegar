<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

use App\Models\Menu;
use App\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:menu.view')->only('index', 'data');
        $this->middleware('permission:menu.create')->only('create', 'store');
        $this->middleware('permission:menu.update')->only('edit', 'update');
        $this->middleware('permission:menu.delete')->only('destroy');
    }

    public function index()
    {
        return view('menu.index');
    }

    public function data()
    {
        $menus = Menu::query()->with('parent')->orderBy('order');
        return DataTables::of($menus)
            ->addIndexColumn()
            ->addColumn('drag_handle', function ($menu) {
                return '<i class="material-icons drag-handle" style="cursor: move;">open_with</i>';
            })
            ->addColumn('parent_name', function ($menu) {
                return $menu->parent ? $menu->parent->name : '-';
            })
            ->addColumn('aksi', function ($menu) {
                $buttons = '';
                if (auth()->user()->hasPermission('menu.update')) {
                    $buttons .= '<a href="' . route('menu.edit', $menu->id) . '" class="btn btn-success"><i class="material-icons">edit</i></a> ';
                }
                if (auth()->user()->hasPermission('menu.delete')) {
                    $buttons .= '<form action="' . route('menu.destroy', $menu->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Hapus menu ini?\')">' .
                               csrf_field() .
                               method_field('DELETE') .
                               '<button type="submit" class="btn btn-danger"><i class="material-icons">delete</i></button></form>';
                }
                return $buttons;
            })
            ->rawColumns(['drag_handle', 'aksi'])
            ->make(true);
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:menus,id',
            'orders.*.order' => 'required|numeric'
        ]);

        foreach ($request->orders as $orderData) {
            Menu::where('id', $orderData['id'])->update(['order' => $orderData['order']]);
        }

        return response()->json(['success' => 'Urutan menu berhasil diperbarui']);
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
