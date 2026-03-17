<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Role;
use App\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class RoleController extends Controller
{
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
                return '<a href="' . route('role.edit', $role->id) . '" class="btn btn-success"><i class="material-icons">edit</i></a> ' .
                       '<form action="' . route('role.destroy', $role->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Hapus role ini?\')">' .
                       csrf_field() .
                       method_field('DELETE') .
                       '<button type="submit" class="btn btn-danger"><i class="material-icons">delete</i></button></form>';
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

        return redirect()->route('role.index')->with('success', 'Role berhasil diupdate');
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
