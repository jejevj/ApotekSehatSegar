@extends('admin.layouts.app')

@section('title', 'Buat Toko Baru')

@section('content')
<div class="block-header">
    <h2>Buat Toko Baru</h2>
</div>

<div class="row clearfix">
    <div class="col-md-8 col-md-offset-2">
        <div class="card">
            <div class="header">
                <h2>Form Toko Baru</h2>
            </div>
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

                <form action="{{ route('admin.stores.store') }}" method="POST">
                    @csrf

                    <h4>Informasi Toko</h4>
                    <hr>

                    <div class="form-group">
                        <label>Nama Toko <span class="text-danger">*</span></label>
                        <div class="form-line">
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Nama toko">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tipe Bisnis <span class="text-danger">*</span></label>
                        <select name="business_type" class="form-control" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="apotek" {{ old('business_type') == 'apotek' ? 'selected' : '' }}>Apotek</option>
                            <option value="retail" {{ old('business_type') == 'retail' ? 'selected' : '' }}>Retail</option>
                            <option value="fnb" {{ old('business_type') == 'fnb' ? 'selected' : '' }}>F&B</option>
                            <option value="general" {{ old('business_type') == 'general' ? 'selected' : '' }}>General</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nama Pemilik</label>
                        <div class="form-line">
                            <input type="text" name="owner_name" class="form-control" value="{{ old('owner_name') }}" placeholder="Nama pemilik toko">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Telepon</label>
                        <div class="form-line">
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="Nomor telepon">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alamat</label>
                        <div class="form-line">
                            <textarea name="address" class="form-control" rows="3" placeholder="Alamat toko">{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <h4 style="margin-top:24px;">Akun Admin Toko</h4>
                    <hr>

                    <div class="form-group">
                        <label>Username Admin <span class="text-danger">*</span></label>
                        <div class="form-line">
                            <input type="text" name="admin_username" class="form-control" value="{{ old('admin_username') }}" required placeholder="Username untuk login">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nama Admin <span class="text-danger">*</span></label>
                        <div class="form-line">
                            <input type="text" name="admin_nama" class="form-control" value="{{ old('admin_nama') }}" required placeholder="Nama lengkap admin">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Password Admin <span class="text-danger">*</span></label>
                        <div class="form-line">
                            <input type="password" name="admin_password" class="form-control" required placeholder="Min. 6 karakter">
                        </div>
                    </div>

                    <div class="m-t-20">
                        <button type="submit" class="btn btn-primary waves-effect">
                            <i class="material-icons">save</i> Buat Toko
                        </button>
                        <a href="{{ route('admin.stores.index') }}" class="btn btn-default waves-effect" style="margin-left:8px;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
