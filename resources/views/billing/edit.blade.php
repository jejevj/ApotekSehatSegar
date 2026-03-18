@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>EDIT BILLING</h2>
            </div>
            <div class="body">
                <form action="{{ route('billing.update', $billing->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <label for="expired_at">Jatuh Tempo</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="datetime-local" name="expired_at" class="form-control"
                                   value="{{ old('expired_at', optional($billing->expired_at)->format('Y-m-d\TH:i')) }}" required />
                        </div>
                    </div>

                    <label for="jumlah_tagihan">Jumlah Tagihan</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="number" name="jumlah_tagihan" class="form-control" value="{{ old('jumlah_tagihan', $billing->jumlah_tagihan) }}" required />
                        </div>
                    </div>

                    <label for="nama_bank">Nama Bank</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="nama_bank" class="form-control" value="{{ old('nama_bank', $billing->nama_bank) }}" required />
                        </div>
                    </div>

                    <label for="no_rek">No. Rekening</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="no_rek" class="form-control" value="{{ old('no_rek', $billing->no_rek) }}" required />
                        </div>
                    </div>

                    <label>Status</label>
                    <div class="form-group">
                        <div class="demo-radio-button">
                            <input name="status" type="radio" id="status_aktif" value="aktif" {{ old('status', $billing->status) === 'aktif' ? 'checked' : '' }} />
                            <label for="status_aktif">Aktif</label>
                            <input name="status" type="radio" id="status_sudah_dibayar" value="sudah_dibayar" {{ old('status', $billing->status) === 'sudah_dibayar' ? 'checked' : '' }} />
                            <label for="status_sudah_dibayar">Sudah Dibayar</label>
                            <input name="status" type="radio" id="status_kedaluwarsa" value="kedaluwarsa" {{ old('status', $billing->status) === 'kedaluwarsa' ? 'checked' : '' }} />
                            <label for="status_kedaluwarsa">Kedaluwarsa</label>
                        </div>
                    </div>

                    <div class="m-t-20">
                        <input type="submit" value="Update" class="btn btn-primary waves-effect">
                        <a href="{{ route('billing.index') }}" class="btn btn-default waves-effect">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
