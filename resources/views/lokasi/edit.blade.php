@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>EDIT LOKASI BARANG</h2>
            </div>
            <div class="body">
                <form action="{{ route('lokasi.update', $lokasi->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <label for="nama_lokasi">Nama Lokasi</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="nama_lokasi" class="form-control" value="{{ $lokasi->nama_lokasi }}" required />
                        </div>
                    </div>

                    <div class="m-t-20">
                        <input type="submit" value="Update" class="btn btn-primary waves-effect">
                        <a href="{{ route('lokasi.index') }}" class="btn btn-default waves-effect">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
