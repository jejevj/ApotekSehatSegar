@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>EDIT LOKASI & RAK BARANG</h2>
            </div>
            <div class="body">
                <form action="{{ route('rak.update', $rak->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <label for="nama_lokasi">Nama Lokasi</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="nama_lokasi" class="form-control" value="{{ $rak->nama_lokasi }}" required />
                        </div>
                    </div>

                    <label for="nama_rak">Nama Rak</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="nama_rak" class="form-control" value="{{ $rak->nama_rak }}" required />
                        </div>
                    </div>

                    <div class="m-t-20">
                        <input type="submit" value="Update" class="btn btn-primary waves-effect">
                        <a href="{{ route('rak.index') }}" class="btn btn-default waves-effect">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
