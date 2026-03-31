@extends('admin.layouts.app')

@section('title', 'Edit Toko: ' . $store->name)

@section('content')
<div class="block-header">
    <h2>Edit Toko: {{ $store->name }}</h2>
</div>

<div class="row clearfix">
    <div class="col-md-8 col-md-offset-2">
        <div class="card">
            <div class="header">
                <h2>Form Edit Toko</h2>
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

                <form action="{{ route('admin.stores.update', $store) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Nama Toko <span class="text-danger">*</span></label>
                        <div class="form-line">
                            <input type="text" name="name" class="form-control" value="{{ old('name', $store->name) }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tipe Bisnis <span class="text-danger">*</span></label>
                        <select name="business_type" class="form-control" required>
                            <option value="apotek" {{ old('business_type', $store->business_type) == 'apotek' ? 'selected' : '' }}>Apotek</option>
                            <option value="retail" {{ old('business_type', $store->business_type) == 'retail' ? 'selected' : '' }}>Retail</option>
                            <option value="fnb" {{ old('business_type', $store->business_type) == 'fnb' ? 'selected' : '' }}>F&B</option>
                            <option value="general" {{ old('business_type', $store->business_type) == 'general' ? 'selected' : '' }}>General</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nama Pemilik</label>
                        <div class="form-line">
                            <input type="text" name="owner_name" class="form-control" value="{{ old('owner_name', $store->owner_name) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Telepon</label>
                        <div class="form-line">
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $store->phone) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alamat</label>
                        <div class="form-line">
                            <textarea name="address" class="form-control" rows="3">{{ old('address', $store->address) }}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Status Aktif</label>
                        <div>
                            <label class="radio-inline">
                                <input type="radio" name="is_active" value="1" {{ old('is_active', $store->is_active ? '1' : '0') == '1' ? 'checked' : '' }}> Aktif
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="is_active" value="0" {{ old('is_active', $store->is_active ? '1' : '0') == '0' ? 'checked' : '' }}> Nonaktif
                            </label>
                        </div>
                    </div>

                    <div class="m-t-20">
                        <button type="submit" class="btn btn-primary waves-effect">
                            <i class="material-icons">save</i> Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.stores.show', $store) }}" class="btn btn-default waves-effect" style="margin-left:8px;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
