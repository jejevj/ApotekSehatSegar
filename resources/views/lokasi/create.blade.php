@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>TAMBAH LOKASI BARANG</h2>
            </div>
            <div class="body">
                <form action="{{ route('lokasi.store') }}" method="POST">
                    @csrf

                    <label for="nama_lokasi">Nama Lokasi</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="nama_lokasi" class="form-control" placeholder="Contoh: Gudang A, Toko Depan, dll" required />
                        </div>
                    </div>

                    <div class="m-t-20">
                        <input type="submit" value="Simpan" class="btn btn-primary waves-effect">
                        <a href="{{ route('lokasi.index') }}" class="btn btn-default waves-effect">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
