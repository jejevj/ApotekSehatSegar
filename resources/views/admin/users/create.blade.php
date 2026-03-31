@extends('admin.layouts.app')

@section('title', 'Tambah User Toko: ' . $store->name)

@section('content')
<div class="block-header">
    <h2>Tambah User — {{ $store->name }}</h2>
</div>

<div class="row clearfix">
    <div class="col-md-8 col-md-offset-2">
        <div class="card">
            <div class="header"><h2>Form Tambah User</h2></div>
            <div class="body">
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="m-b-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('admin.stores.users.store', $store) }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="form-line">
                            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Username <span class="text-danger">*</span></label>
                        <div class="form-line">
                            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Password <span class="text-danger">*</span></label>
                        <div class="form-line">
                            <input type="password" name="password" class="form-control" required placeholder="Min. 6 karakter">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Level <span class="text-danger">*</span></label>
                        <select name="level" class="form-control" required>
                            <option value="">-- Pilih Level --</option>
                            <option value="admin" {{ old('level') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="kasir" {{ old('level') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                            <option value="staff" {{ old('level') == 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Role <span class="text-danger">*</span></label>
                        <select name="role_id" class="form-control" required>
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="m-t-20">
                        <button type="submit" class="btn btn-primary waves-effect">
                            <i class="material-icons">save</i> Simpan
                        </button>
                        <a href="{{ route('admin.stores.users', $store) }}" class="btn btn-default waves-effect" style="margin-left:8px;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
