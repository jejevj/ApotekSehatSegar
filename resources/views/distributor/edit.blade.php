@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>EDIT DISTRIBUTOR</h2>
    </div>

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>FORM EDIT DISTRIBUTOR</h2>
                </div>
                <div class="body">
                    <form action="{{ route('distributor.update', $distributor->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <label for="nama">Nama Distributor</label>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" id="nama" name="nama" class="form-control" value="{{ $distributor->nama }}" required>
                            </div>
                        </div>

                        <label for="telepon">Telepon</label>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" id="telepon" name="telepon" class="form-control" value="{{ $distributor->telepon }}">
                            </div>
                        </div>

                        <label for="alamat">Alamat</label>
                        <div class="form-group">
                            <div class="form-line">
                                <textarea id="alamat" name="alamat" rows="3" class="form-control no-resize">{{ $distributor->alamat }}</textarea>
                            </div>
                        </div>

                        <label for="keterangan">Keterangan</label>
                        <div class="form-group">
                            <div class="form-line">
                                <textarea id="keterangan" name="keterangan" rows="2" class="form-control no-resize">{{ $distributor->keterangan }}</textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary m-t-15 waves-effect">SIMPAN PERUBAHAN</button>
                        <a href="{{ route('distributor.index') }}" class="btn btn-default m-t-15 waves-effect">KEMBALI</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection