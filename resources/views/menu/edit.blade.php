@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>EDIT MENU</h2>
            </div>
            <div class="body">
                <form action="{{ route('menu.update', $menu->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <label for="name">Nama Menu</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="name" class="form-control" value="{{ $menu->name }}" placeholder="Masukkan Nama Menu" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="icon">Icon (Material Icon Name)</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="icon" class="form-control" value="{{ $menu->icon }}" placeholder="Contoh: home, person, settings" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label for="route_name">Route Name</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="route_name" class="form-control" value="{{ $menu->route_name }}" placeholder="Contoh: barang.index" />
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
                                        <option value="{{ $perm->slug }}" {{ $menu->permission_slug == $perm->slug ? 'selected' : '' }}>{{ $perm->name }} ({{ $perm->slug }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="url">URL (Alternative to Route)</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="url" class="form-control" value="{{ $menu->url }}" placeholder="Contoh: /laporan/statistik" />
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
                                        <option value="{{ $parent->id }}" {{ $menu->parent_id == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="order">Urutan Tampil</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="number" name="order" class="form-control" value="{{ $menu->order }}" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="target">Target (Modal ID)</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="target" class="form-control" value="{{ $menu->target }}" placeholder="Contoh: #smallModal" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <label>Akses Role</label>
                    <div class="demo-checkbox">
                        @foreach($roles as $role)
                        <input type="checkbox" id="role_{{ $role->id }}" name="roles[]" value="{{ $role->id }}" class="filled-in chk-col-pink" {{ in_array($role->id, $menuRoles) ? 'checked' : '' }}>
                        <label for="role_{{ $role->id }}">{{ $role->name }}</label>
                        @endforeach
                    </div>

                    <div class="m-t-20">
                        <input type="submit" value="Update" class="btn btn-primary waves-effect">
                        <a href="{{ route('menu.index') }}" class="btn btn-default waves-effect">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
