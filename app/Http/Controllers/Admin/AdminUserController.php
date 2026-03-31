<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Store $store)
    {
        $users = User::withoutGlobalScopes()
            ->where('store_id', $store->id)
            ->with('role')
            ->get();

        return view('admin.users.index', compact('store', 'users'));
    }

    public function create(Store $store)
    {
        // Tampilkan role global (store_id = null) + role spesifik toko ini
        $roles = Role::withoutGlobalScopes()
            ->where('slug', '!=', 'super_admin')
            ->where(function ($q) use ($store) {
                $q->whereNull('store_id')->orWhere('store_id', $store->id);
            })
            ->get();

        return view('admin.users.create', compact('store', 'roles'));
    }

    public function store(Store $store, Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'nama'     => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'role_id'  => 'required|exists:roles,id',
            'level'    => 'required|string|in:admin,kasir,staff',
        ]);

        // Pastikan role bukan super_admin
        $role = Role::withoutGlobalScopes()->findOrFail($validated['role_id']);
        if ($role->slug === 'super_admin') {
            return back()->withErrors(['role_id' => 'Tidak dapat menetapkan role super_admin ke user toko.']);
        }

        User::create([
            'store_id' => $store->id,
            'username' => $validated['username'],
            'nama'     => $validated['nama'],
            'password' => Hash::make($validated['password']),
            'role_id'  => $validated['role_id'],
            'level'    => $validated['level'],
        ]);

        return redirect()->route('admin.stores.users', $store)
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(Store $store, User $user)
    {
        // Tampilkan role global (store_id = null) + role spesifik toko ini
        $roles = Role::withoutGlobalScopes()
            ->where('slug', '!=', 'super_admin')
            ->where(function ($q) use ($store) {
                $q->whereNull('store_id')->orWhere('store_id', $store->id);
            })
            ->get();

        return view('admin.users.edit', compact('store', 'user', 'roles'));
    }

    public function update(Store $store, User $user, Request $request)
    {
        $validated = $request->validate([
            'nama'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role_id'  => 'required|exists:roles,id',
            'level'    => 'required|string|in:admin,kasir,staff',
        ]);

        // Pastikan role bukan super_admin
        $role = Role::withoutGlobalScopes()->findOrFail($validated['role_id']);
        if ($role->slug === 'super_admin') {
            return back()->withErrors(['role_id' => 'Tidak dapat menetapkan role super_admin ke user toko.']);
        }

        $data = [
            'nama'     => $validated['nama'],
            'username' => $validated['username'],
            'role_id'  => $validated['role_id'],
            'level'    => $validated['level'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.stores.users', $store)
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Store $store, User $user)
    {
        // Tolak jika satu-satunya admin di toko tersebut
        $adminCount = User::withoutGlobalScopes()
            ->where('store_id', $store->id)
            ->where('level', 'admin')
            ->count();

        if ($adminCount <= 1 && $user->level === 'admin') {
            return back()->with('error', 'Tidak dapat menghapus satu-satunya admin toko.');
        }

        $user->delete();

        return redirect()->route('admin.stores.users', $store)
            ->with('success', 'User berhasil dihapus.');
    }
}
