@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>EDIT PELANGGAN</h2>
            </div>
            <div class="body">
                <form action="{{ route('pelanggan.update', $pelanggan->kode_pelanggan) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <label for="nama">Nama</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="nama" class="form-control" value="{{ $pelanggan->nama }}" placeholder="Masukkan Nama Pelanggan" required />
                        </div>
                    </div>

                    <label for="alamat">Alamat</label>
                    <div class="form-group">
                        <div class="form-line">
                            <textarea name="alamat" rows="4" class="form-control no-resize" placeholder="Masukkan Alamat" required>{{ $pelanggan->alamat }}</textarea>
                        </div>
                    </div>

                    <label for="telpon">Telepon</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="telpon" class="form-control" value="{{ $pelanggan->telpon }}" placeholder="Masukkan Nomor Telepon" required />
                        </div>
                    </div>

                    <input type="submit" name="simpan" value="Simpan" class="btn btn-primary">
                    <a href="{{ route('pelanggan.index') }}" class="btn btn-default">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
