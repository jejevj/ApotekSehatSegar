@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>PENGATURAN PROFIL WEBSITE</h2>
            </div>
            <div class="body">
                <form action="{{ route('setting.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label for="nama_aplikasi">Nama Aplikasi / Website</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="nama_aplikasi" class="form-control" value="{{ $setting->nama_aplikasi }}" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="nama_pemilik">Nama Pemilik / Apoteker</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="nama_pemilik" class="form-control" value="{{ $setting->nama_pemilik }}" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="telepon">Nomor Telepon</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="telepon" class="form-control" value="{{ $setting->telepon }}" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="email">Email</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="email" name="email" class="form-control" value="{{ $setting->email }}" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="alamat">Alamat Lengkap</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <textarea name="alamat" rows="3" class="form-control no-resize">{{ $setting->alamat }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="logo">Logo Website</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="file" name="logo" class="form-control" onchange="previewImage(this, 'logo-preview')" />
                                </div>
                            </div>
                            <div class="m-t-10">
                                <img id="logo-preview" src="{{ $setting->logo ? asset('images/' . $setting->logo) : 'https://placehold.co/150x150?text=No+Logo' }}" alt="Logo" style="max-width: 150px; border: 1px solid #eee; padding: 5px; border-radius: 5px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="favicon">Favicon (Icon Browser)</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="file" name="favicon" class="form-control" onchange="previewImage(this, 'favicon-preview')" />
                                </div>
                            </div>
                            <div class="m-t-10">
                                <img id="favicon-preview" src="{{ $setting->favicon ? asset('images/' . $setting->favicon) : 'https://placehold.co/50x50?text=Icon' }}" alt="Favicon" style="max-width: 50px; border: 1px solid #eee; padding: 5px; border-radius: 5px;">
                            </div>
                        </div>
                    </div>

                    <div class="row m-t-20">
                        <div class="col-md-12">
                            <label for="footer_text">Teks Footer</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="footer_text" class="form-control" value="{{ $setting->footer_text }}" placeholder="Contoh: Copyright © 2026 Apotek App" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="m-t-20 text-right">
                        <button type="submit" class="btn btn-primary btn-lg waves-effect"><i class="material-icons">save</i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#' + previewId).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
