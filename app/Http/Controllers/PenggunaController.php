<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class PenggunaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:pengguna.view')->only('index', 'data');
        $this->middleware('permission:pengguna.create')->only('create', 'store');
        $this->middleware('permission:pengguna.update')->only('edit', 'update');
        $this->middleware('permission:pengguna.delete')->only('destroy');
    }

    public function index()
    {
        return view('pengguna.index');
    }

    public function data()
    {
        $storeId = auth()->user()->store_id;
        $users = User::with('role')
            ->where('store_id', $storeId)
            ->select('users.*');

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('role_name', function ($user) {
                return $user->role ? $user->role->name : '-';
            })
            ->addColumn('aksi', function ($user) {
                $buttons = '';
                if (auth()->user()->hasPermission('pengguna.update')) {
                    $buttons .= '<a href="' . route('pengguna.edit', $user->id) . '" class="btn btn-success"><i class="material-icons">edit</i></a>';
                }
                if (auth()->user()->hasPermission('pengguna.delete')) {
                    $buttons .= ' <form action="' . route('pengguna.destroy', $user->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Apakah Anda Yakin Akan Mengahapus Data ini???\')">' .
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
        $storeId = auth()->user()->store_id;
        $roles = Role::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
            ->where('slug', '!=', 'super_admin')
            ->where(function ($q) use ($storeId) {
                $q->whereNull('store_id')->orWhere('store_id', $storeId);
            })
            ->get();
        return view('pengguna.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'nama' => 'required|string|max:255',
            'password' => 'required|string|min:4',
            'level' => 'required|in:admin,kasir,billing',
            'role_id' => 'required|exists:roles,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $storeId = auth()->user()->store_id;

        // Batasi hanya 1 akun dengan role billing
        $role = Role::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)->find($validated['role_id']);
        if ($role && $role->slug === 'billing') {
            $existing = User::where('store_id', $storeId)->where('role_id', $role->id)->count();
            if ($existing >= 1) {
                return redirect()->back()->withInput()->with('error', 'Akun billing sudah ada. Hanya boleh 1 akun billing.');
            }
        }

        $data = [
            'store_id' => $storeId,
            'username' => $validated['username'],
            'nama' => $validated['nama'],
            'password' => Hash::make($validated['password']),
            'level' => $validated['level'],
            'role_id' => $validated['role_id'],
        ];

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $fotoName = time() . '.' . $foto->getClientOriginalExtension();
            $foto->move(public_path('images'), $fotoName);
            $data['foto'] = $fotoName;
        }

        User::create($data);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil ditambahkan');
    }

    public function edit($id)
    {
        $storeId = auth()->user()->store_id;
        $user = User::where('store_id', $storeId)->findOrFail($id);
        $roles = Role::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
            ->where('slug', '!=', 'super_admin')
            ->where(function ($q) use ($storeId) {
                $q->whereNull('store_id')->orWhere('store_id', $storeId);
            })
            ->get();
        return view('pengguna.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $storeId = auth()->user()->store_id;
        $user = User::where('store_id', $storeId)->findOrFail($id);

        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'nama' => 'required|string|max:255',
            'password' => 'nullable|string|min:4',
            'level' => 'required|in:admin,kasir,billing',
            'role_id' => 'required|exists:roles,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Batasi hanya 1 akun dengan role billing (kecuali user ini sendiri)
        $role = Role::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)->find($validated['role_id']);
        if ($role && $role->slug === 'billing') {
            $existing = User::where('store_id', $storeId)->where('role_id', $role->id)->where('id', '<>', $user->id)->count();
            if ($existing >= 1) {
                return redirect()->back()->withInput()->with('error', 'Akun billing sudah ada. Hanya boleh 1 akun billing.');
            }
        }

        $user->username = $validated['username'];
        $user->nama = $validated['nama'];
        $user->level = $validated['level'];
        $user->role_id = $validated['role_id'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('foto')) {
            if ($user->foto && file_exists(public_path('images/' . $user->foto))) {
                unlink(public_path('images/' . $user->foto));
            }

            $foto = $request->file('foto');
            $fotoName = time() . '.' . $foto->getClientOriginalExtension();
            $foto->move(public_path('images'), $fotoName);
            $user->foto = $fotoName;
        }

        $user->save();

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil diupdate');
    }

    public function destroy($id)
    {
        $storeId = auth()->user()->store_id;
        $user = User::where('store_id', $storeId)->findOrFail($id);

        if (auth()->id() === $user->id) {
            return redirect()->route('pengguna.index')->with('error', 'Tidak bisa menghapus akun yang sedang digunakan');
        }

        $user->delete();

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil dihapus');
    }
}
