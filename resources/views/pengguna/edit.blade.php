@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>EDIT PENGGUNA</h2>
            </div>
            <div class="body">
                <form action="{{ route('pengguna.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <label for="username">Username</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="username" class="form-control" value="{{ $user->username }}" placeholder="Masukkan Username" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="nama">Nama Lengkap</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="nama" class="form-control" value="{{ $user->nama }}" placeholder="Masukkan Nama Lengkap" required />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="password">Password (Kosongkan jika tidak ingin diubah)</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="password" name="password" class="form-control" placeholder="Masukkan Password Baru" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="level">Level (Legacy)</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <select name="level" class="form-control show-tick" required>
                                        <option value="admin" {{ $user->level == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="kasir" {{ $user->level == 'kasir' ? 'selected' : '' }}>Kasir</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="role_id">Role Akses</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <select name="role_id" class="form-control show-tick" required>
                                        <option value="">-- Pilih Role --</option>
                                        @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="foto">Foto Profil</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="file" name="foto" class="form-control" />
                                </div>
                            </div>
                            @if($user->foto)
                            <div class="m-t-10">
                                <img src="{{ asset('images/' . $user->foto) }}" alt="Current Foto" style="max-width: 100px; border-radius: 5px;">
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="m-t-20">
                        <input type="submit" value="Update" class="btn btn-primary waves-effect">
                        <a href="{{ route('pengguna.index') }}" class="btn btn-default waves-effect">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
