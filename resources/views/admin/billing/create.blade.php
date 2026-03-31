@extends('admin.layouts.app')

@section('title', 'Tambah Billing — ' . $store->name)

@section('content')
<div class="block-header">
    <h2>Tambah Billing — {{ $store->name }}</h2>
</div>

<div class="row clearfix">
    <div class="col-md-8 col-md-offset-2">
        <div class="card">
            <div class="header"><h2>Form Tambah Billing</h2></div>
            <div class="body">
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="m-b-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('admin.stores.billing.store', $store) }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>Tanggal Expired <span class="text-danger">*</span></label>
                        <div class="form-line">
                            <input type="date" name="expired_at" class="form-control" value="{{ old('expired_at') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Jumlah Tagihan (Rp) <span class="text-danger">*</span></label>
                        <div class="form-line">
                            <input type="number" name="jumlah_tagihan" class="form-control" value="{{ old('jumlah_tagihan', 0) }}" min="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nama Bank</label>
                        <div class="form-line">
                            <input type="text" name="nama_bank" class="form-control" value="{{ old('nama_bank') }}" placeholder="Contoh: BCA, Mandiri">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nomor Rekening</label>
                        <div class="form-line">
                            <input type="text" name="no_rek" class="form-control" value="{{ old('no_rek') }}" placeholder="Nomor rekening tujuan">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status', 'nonaktif') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>

                    <div class="m-t-20">
                        <button type="submit" class="btn btn-primary waves-effect">
                            <i class="material-icons">save</i> Simpan
                        </button>
                        <a href="{{ route('admin.stores.billing', $store) }}" class="btn btn-default waves-effect" style="margin-left:8px;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
