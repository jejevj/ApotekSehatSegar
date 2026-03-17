@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>EDIT ROLE</h2>
            </div>
            <div class="body">
                <form action="{{ route('role.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <label for="name">Nama Role</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="name" class="form-control" value="{{ $role->name }}" placeholder="Masukkan Nama Role" required />
                        </div>
                    </div>

                    <label>Hak Akses (Permissions)</label>
                    <div class="row clearfix">
                        @foreach($permissions as $feature => $perms)
                        <div class="col-md-3">
                            <div class="card" style="box-shadow: none; border: 1px solid #eee;">
                                <div class="header" style="padding: 10px;">
                                    <h4 style="margin:0; font-size: 14px;">{{ strtoupper($feature) }}</h4>
                                </div>
                                <div class="body" style="padding: 10px;">
                                    @foreach($perms as $perm)
                                    <div class="demo-checkbox">
                                        <input type="checkbox" id="perm_{{ $perm->id }}" name="permissions[]" value="{{ $perm->id }}" class="filled-in chk-col-pink" {{ in_array($perm->id, $rolePermissions) ? 'checked' : '' }}>
                                        <label for="perm_{{ $perm->id }}">{{ ucfirst($perm->action) }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="m-t-20">
                        <input type="submit" value="Update" class="btn btn-primary waves-effect">
                        <a href="{{ route('role.index') }}" class="btn btn-default waves-effect">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
