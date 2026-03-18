@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>OPNAME: {{ $opname->kode_opname }} (DRAFT)</h2>
            </div>
            <div class="body">
                <form action="{{ route('opname.update', $opname->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-4">
                            <label for="tanggal">Tanggal & Waktu</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="datetime-local" name="tanggal" class="form-control" value="{{ $opname->tanggal->format('Y-m-d\TH:i') }}" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label for="catatan">Catatan</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="catatan" class="form-control" value="{{ $opname->catatan }}" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="m-t-10">
                        <input type="submit" value="Simpan Header" class="btn btn-primary waves-effect">
                        <a href="{{ route('opname.index') }}" class="btn btn-default waves-effect">Kembali</a>
                    </div>
                </form>

                <hr>

                <form action="{{ route('opname.addItem', $opname->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-5">
                            <label for="kode_barcode">Barcode</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="kode_barcode" class="form-control" placeholder="Scan / input barcode" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="stok_fisik">Stok Fisik</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="number" name="stok_fisik" class="form-control" min="0" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4" style="padding-top: 25px;">
                            <button type="submit" class="btn btn-success waves-effect">
                                <i class="material-icons">add</i> Tambah / Update
                            </button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Barcode</th>
                                <th>Nama Barang</th>
                                <th width="10%">Stok Sistem</th>
                                <th width="10%">Stok Fisik</th>
                                <th width="10%">Selisih</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($opname->items as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $item->kode_barcode }}</td>
                                <td>{{ $item->nama_barang }}</td>
                                <td>{{ $item->stok_sistem }}</td>
                                <td>
                                    <form action="{{ route('opname.updateItem', [$opname->id, $item->id]) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" name="stok_fisik" value="{{ $item->stok_fisik }}" min="0" style="width:90px;" required>
                                        <button type="submit" class="btn btn-xs btn-primary">OK</button>
                                    </form>
                                </td>
                                <td>{{ $item->selisih }}</td>
                                <td>
                                    <form action="{{ route('opname.removeItem', [$opname->id, $item->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus item ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger"><i class="material-icons">delete</i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="m-t-20">
                    <form action="{{ route('opname.finish', $opname->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Selesaikan opname dan perbarui stok?')">
                        @csrf
                        <button type="submit" class="btn btn-success waves-effect"><i class="material-icons">check</i> Selesai</button>
                    </form>
                    <form action="{{ route('opname.cancel', $opname->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Batalkan opname?')">
                        @csrf
                        <button type="submit" class="btn btn-warning waves-effect"><i class="material-icons">close</i> Batalkan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

