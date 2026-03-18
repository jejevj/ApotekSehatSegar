@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>UBAH BARANG</h2>
            </div>
            <div class="body">
                <form action="{{ route('barang.update', $barang->kode_barcode) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <label for="kode_barcode">Barcode</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="kode_barcode" class="form-control" value="{{ $barang->kode_barcode }}" readonly />
                        </div>
                    </div>

                    <label for="nama_barang">Nama Barang</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="nama_barang" class="form-control" value="{{ $barang->nama_barang }}" required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="unit_id">Satuan</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <select name="unit_id" id="unit_id" class="form-control show-tick" required>
                                        <option value="">-- Pilih Satuan --</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ (int) $selectedUnitId === (int) $unit->id ? 'selected' : '' }}>{{ $unit->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="isi">Isi (Opsional)</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="number" name="isi" id="isi" class="form-control" value="{{ (int) ($barang->isi ?? 1) }}" min="1" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <label for="harga_beli">Harga Beli</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="number" name="harga_beli" class="form-control" value="{{ $barang->harga_beli }}" required />
                        </div>
                    </div>

                    <label for="stok">Stok</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="number" name="stok" class="form-control" value="{{ $barang->stok }}" required />
                        </div>
                    </div>

                    <label for="harga_jual">Harga Jual</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="number" name="harga_jual" class="form-control" value="{{ $barang->harga_jual }}" required />
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
