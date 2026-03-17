<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class PenggunaController extends Controller
{
    public function index()
    {
        return view('pengguna.index');
    }

    public function data()
    {
        $users = User::with('role')->select('users.*');

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('role_name', function ($user) {
                return $user->role ? $user->role->name : '-';
            })
            ->addColumn('aksi', function ($user) {
                $editUrl = route('pengguna.edit', $user->id);
                $destroyUrl = route('pengguna.destroy', $user->id);

                $editButton = '<a href="' . $editUrl . '" class="btn btn-success"><i class="material-icons">edit</i></a>';

                $deleteButton = '<form action="' . $destroyUrl . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Apakah Anda Yakin Akan Mengahapus Data ini???\')">' .
                    csrf_field() .
                    method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger"><i class="material-icons">delete</i></button></form>';

                return $editButton . ' ' . $deleteButton;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        $roles = Role::all();
        return view('pengguna.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'nama' => 'required|string|max:255',
            'password' => 'required|string|min:4',
            'level' => 'required|in:admin,kasir',
            'role_id' => 'required|exists:roles,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
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
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('pengguna.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'nama' => 'required|string|max:255',
            'password' => 'nullable|string|min:4',
            'level' => 'required|in:admin,kasir',
            'role_id' => 'required|exists:roles,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->username = $validated['username'];
        $user->nama = $validated['nama'];
        $user->level = $validated['level'];
        $user->role_id = $validated['role_id'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
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
        $user = User::findOrFail($id);

        if (auth()->id() === $user->id) {
            return redirect()->route('pengguna.index')->with('error', 'Tidak bisa menghapus akun yang sedang digunakan');
        }

        $user->delete();

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil dihapus');
    }
}
