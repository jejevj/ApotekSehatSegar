@extends('admin.layouts.app')

@section('title', 'Konfigurasi Bisnis — ' . $store->name)

@section('content')
<div class="block-header">
    <h2>Konfigurasi Bisnis — {{ $store->name }}</h2>
</div>

<div class="row clearfix">
    <div class="col-md-10 col-md-offset-1">
        <div class="card">
            <div class="header"><h2>Edit Konfigurasi</h2></div>
            <div class="body">
                @if($configs->isEmpty())
                <div class="alert alert-info">Belum ada konfigurasi untuk toko ini.</div>
                @endif

                <form action="{{ route('admin.stores.business-config.update', $store) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @if($configs->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="35%">Key</th>
                                    <th width="15%">Tipe</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($configs as $key => $config)
                                <tr>
                                    <td><code>{{ $key }}</code></td>
                                    <td><span class="label label-default">{{ $config->type }}</span></td>
                                    <td>
                                        @if($config->type === 'boolean')
                                        <select name="{{ $key }}" class="form-control input-sm">
                                            <option value="1" {{ $config->value == '1' ? 'selected' : '' }}>Ya (true)</option>
                                            <option value="0" {{ $config->value == '0' ? 'selected' : '' }}>Tidak (false)</option>
                                        </select>
                                        @else
                                        <div class="form-line">
                                            <input type="text" name="{{ $key }}" class="form-control input-sm" value="{{ $config->value }}">
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="m-t-20">
                        <button type="submit" class="btn btn-primary waves-effect">
                            <i class="material-icons">save</i> Simpan Konfigurasi
                        </button>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-xs-12">
        <a href="{{ route('admin.stores.show', $store) }}" class="btn btn-default waves-effect">
            <i class="material-icons">arrow_back</i> Kembali ke Detail Toko
        </a>
    </div>
</div>
@endsection
