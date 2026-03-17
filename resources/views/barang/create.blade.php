@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>TAMBAH BARANG</h2>
            </div>
            <div class="body">
                <form action="{{ route('barang.store') }}" method="POST">
                    @csrf
                    <label for="kode_barcode">Barcode</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="kode_barcode" class="form-control" placeholder="Masukkan Barcode" required />
                        </div>
                    </div>

                    <label for="nama_barang">Nama Barang</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="nama_barang" class="form-control" placeholder="Masukkan Nama Barang" required />
                        </div>
                    </div>

                    <label for="satuan">Satuan</label>
                    <div class="form-group">
                        <div class="form-line">
                            <select name="satuan" class="form-control show-tick">
                                <option value="">-- Pilih Satuan --</option>
                                <option value="BOTOL">BOTOL</option>
                                <option value="STRIP">STRIP</option>
                                <option value="PCS">PCS</option>
                                <option value="BOX">BOX</option>
                            </select>
                        </div>
                    </div>

                    <label for="harga_beli">Harga Beli</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="number" name="harga_beli" class="form-control" placeholder="Masukkan Harga Beli" required />
                        </div>
                    </div>

                    <label for="stok">Stok</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="number" name="stok" class="form-control" placeholder="Masukkan Stok" required />
                        </div>
                    </div>

                    <label for="harga_jual">Harga Jual</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="number" name="harga_jual" class="form-control" placeholder="Masukkan Harga Jual" required />
                        </div>
                    </div>

                    <input type="submit" name="simpan" value="Simpan" class="btn btn-primary">
                    <a href="{{ route('barang.index') }}" class="btn btn-default">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
