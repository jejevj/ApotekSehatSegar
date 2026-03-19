@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>TAMBAH ROLE</h2>
            </div>
            <div class="body">
                <form action="{{ route('role.store') }}" method="POST">
                    @csrf
                    <label for="name">Nama Role</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="name" class="form-control" placeholder="Masukkan Nama Role" required />
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
                                    <div class="row">
                                        @foreach($perms as $perm)
                                        <div class="col-md-12">
                                            <div class="demo-checkbox">
                                                <input type="checkbox" id="perm_{{ $perm->id }}" name="permissions[]" value="{{ $perm->id }}" class="filled-in chk-col-pink">
                                                <label for="perm_{{ $perm->id }}">{{ $perm->name }}</label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="m-t-20">
                        <input type="submit" value="Simpan" class="btn btn-primary waves-effect">
                        <a href="{{ route('role.index') }}" class="btn btn-default waves-effect">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
