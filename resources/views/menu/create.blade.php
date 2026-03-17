@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>TAMBAH MENU</h2>
            </div>
            <div class="body">
                <form action="{{ route('menu.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label for="name">Nama Menu</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="name" class="form-control" placeholder="Masukkan Nama Menu" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="icon">Icon (Material Icon Name)</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="icon" class="form-control" placeholder="Contoh: home, person, settings" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label for="route_name">Route Name</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="route_name" class="form-control" placeholder="Contoh: barang.index" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="permission_slug">Permission Slug (Optional)</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <select name="permission_slug" class="form-control show-tick">
                                        <option value="">-- Tanpa Permission --</option>
                                        @foreach($permissions as $perm)
                                        <option value="{{ $perm->slug }}">{{ $perm->name }} ({{ $perm->slug }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="url">URL (Alternative to Route)</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="url" class="form-control" placeholder="Contoh: /laporan/statistik" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label for="parent_id">Parent Menu</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <select name="parent_id" class="form-control show-tick">
                                        <option value="">-- Tanpa Parent (Menu Utama) --</option>
                                        @foreach($parentMenus as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="order">Urutan Tampil</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="number" name="order" class="form-control" value="0" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="target">Target (Modal ID)</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="target" class="form-control" placeholder="Contoh: #smallModal" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <label>Akses Role</label>
                    <div class="demo-checkbox">
                        @foreach($roles as $role)
                        <input type="checkbox" id="role_{{ $role->id }}" name="roles[]" value="{{ $role->id }}" class="filled-in chk-col-pink">
                        <label for="role_{{ $role->id }}">{{ $role->name }}</label>
                        @endforeach
                    </div>

                    <div class="m-t-20">
                        <input type="submit" value="Simpan" class="btn btn-primary waves-effect">
                        <a href="{{ route('menu.index') }}" class="btn btn-default waves-effect">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
