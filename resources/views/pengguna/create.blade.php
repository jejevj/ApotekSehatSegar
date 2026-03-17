@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>TAMBAH PENGGUNA</h2>
            </div>
            <div class="body">
                <form action="{{ route('pengguna.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label for="username">Username</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="nama">Nama Lengkap</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="nama" class="form-control" placeholder="Masukkan Nama Lengkap" required />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="password">Password</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="level">Level (Legacy)</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <select name="level" class="form-control show-tick" required>
                                        <option value="admin">Admin</option>
                                        <option value="kasir">Kasir</option>
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
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
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
                        </div>
                    </div>

                    <div class="m-t-20">
                        <input type="submit" value="Simpan" class="btn btn-primary waves-effect">
                        <a href="{{ route('pengguna.index') }}" class="btn btn-default waves-effect">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
