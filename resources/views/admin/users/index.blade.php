@extends('admin.layouts.app')

@section('title', 'Pengguna Toko: ' . $store->name)

@section('content')
<div class="block-header">
    <h2>Pengguna Toko: {{ $store->name }}</h2>
</div>

<div class="row clearfix">
    <div class="col-xs-12">
        <div class="card">
            <div class="header">
                <h2>Daftar Pengguna</h2>
                <ul class="header-dropdown m-r--5">
                    <li>
                        <a href="{{ route('admin.stores.users.create', $store) }}" class="btn btn-success btn-sm waves-effect">
                            <i class="material-icons">person_add</i> Tambah User
                        </a>
                    </li>
                </ul>
            </div>
            <div class="body table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Level</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->nama }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->level }}</td>
                            <td>{{ $user->role->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.stores.users.edit', [$store, $user]) }}" class="btn btn-warning btn-xs waves-effect" title="Edit">
                                    <i class="material-icons">edit</i>
                                </a>
                                <form action="{{ route('admin.stores.users.destroy', [$store, $user]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus user {{ addslashes($user->nama) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs waves-effect" title="Hapus">
                                        <i class="material-icons">delete</i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada pengguna.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-xs-12">
        <a href="{{ route('admin.stores.show', $store) }}" class="btn btn-default waves-effect">
            <i class="material-icons">arrow_back</i> Kembali ke Detail Toko
        </a>
    </div>
</div>
@endsection
