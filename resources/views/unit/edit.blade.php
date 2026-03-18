@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>EDIT SATUAN BARANG</h2>
            </div>
            <div class="body">
                <form action="{{ route('unit.update', $unit->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <label for="nama">Nama Satuan</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="nama" class="form-control" value="{{ $unit->nama }}" required />
                        </div>
                    </div>

                    <label for="is_single">Apakah satuan ini satu unit (default)?</label>
                    <div class="form-group">
                        <div class="demo-radio-button">
                            <input name="is_single" type="radio" id="is_single_1" value="1" {{ $unit->is_single ? 'checked' : '' }} />
                            <label for="is_single_1">Ya</label>
                            <input name="is_single" type="radio" id="is_single_0" value="0" {{ !$unit->is_single ? 'checked' : '' }} />
                            <label for="is_single_0">Tidak</label>
                        </div>
                    </div>

                    <div class="m-t-20">
                        <input type="submit" value="Update" class="btn btn-primary waves-effect">
                        <a href="{{ route('unit.index') }}" class="btn btn-default waves-effect">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

