<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Role;
use App\Models\Permission;
use App\Models\Menu;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:role.view')->only('index', 'data');
        $this->middleware('permission:role.create')->only('create', 'store');
        $this->middleware('permission:role.update')->only('edit', 'update');
        $this->middleware('permission:role.delete')->only('destroy');
    }

    public function index()
    {
        return view('role.index');
    }

    public function data()
    {
        $roles = Role::query();
        return DataTables::of($roles)
            ->addIndexColumn()
            ->addColumn('aksi', function ($role) {
                $buttons = '';
                if (auth()->user()->hasPermission('role.update')) {
                    $buttons .= '<a href="' . route('role.edit', $role->id) . '" class="btn btn-success"><i class="material-icons">edit</i></a> ';
                }
                if (auth()->user()->hasPermission('role.delete')) {
                    $buttons .= '<form action="' . route('role.destroy', $role->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Hapus role ini?\')">' .
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
        $permissions = Permission::all()->groupBy('feature');
        return view('role.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        $role->permissions()->sync($request->permissions);
        $this->syncMenusForRole($role);

        return redirect()->route('role.index')->with('success', 'Role berhasil ditambahkan');
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::all()->groupBy('feature');
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('role.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'required|array'
        ]);

        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        $role->permissions()->sync($request->permissions);
        $this->syncMenusForRole($role);

        return redirect()->route('role.index')->with('success', 'Role berhasil diupdate');
    }

    private function syncMenusForRole(Role $role): void
    {
        $role->loadMissing('permissions');
        $permissionSlugs = $role->permissions->pluck('slug')->unique()->values();

        $menuIds = Menu::query()
            ->whereNull('permission_slug')
            ->orWhereIn('permission_slug', $permissionSlugs)
            ->pluck('id')
            ->unique()
            ->values()
            ->all();

        $role->menus()->sync($menuIds);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        if ($role->slug === 'admin') {
            return redirect()->route('role.index')->with('error', 'Role admin tidak dapat dihapus');
        }
        $role->delete();
        return redirect()->route('role.index')->with('success', 'Role berhasil dihapus');
    }
}
