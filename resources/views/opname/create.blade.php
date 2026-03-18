@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>BUAT OPNAME</h2>
            </div>
            <div class="body">
                <form action="{{ route('opname.store') }}" method="POST">
                    @csrf

                    <label for="tanggal">Tanggal & Waktu</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="datetime-local" name="tanggal" class="form-control" value="{{ date('Y-m-d\TH:i') }}" required />
                        </div>
                    </div>

                    <label for="catatan">Catatan</label>
                    <div class="form-group">
                        <div class="form-line">
                            <textarea name="catatan" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="m-t-20">
                        <input type="submit" value="Mulai" class="btn btn-primary waves-effect">
                        <a href="{{ route('opname.index') }}" class="btn btn-default waves-effect">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

