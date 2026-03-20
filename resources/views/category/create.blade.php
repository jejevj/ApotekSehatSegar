@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>TAMBAH KATEGORI BARANG</h2>
            </div>
            <div class="body">
                <form action="{{ route('category.store') }}" method="POST">
                    @csrf

                    <label for="nama_kategori">Nama Kategori</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Obat Bebas, Obat Keras, Alat Kesehatan" required />
                        </div>
                    </div>

                    <label for="keterangan">Keterangan (Opsional)</label>
                    <div class="form-group">
                        <div class="form-line">
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Deskripsi singkat kategori"></textarea>
                        </div>
                    </div>

                    <div class="m-t-20">
                        <input type="submit" value="Simpan" class="btn btn-primary waves-effect">
                        <a href="{{ route('category.index') }}" class="btn btn-default waves-effect">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
