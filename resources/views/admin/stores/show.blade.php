@extends('admin.layouts.app')

@section('title', 'Detail Toko: ' . $store->name)

@section('content')
<div class="block-header">
    <h2>Detail Toko: {{ $store->name }}</h2>
</div>

<div class="row clearfix">
    <!-- Info Toko -->
    <div class="col-md-6">
        <div class="card">
            <div class="header">
                <h2>Informasi Toko</h2>
                <ul class="header-dropdown m-r--5">
                    <li>
                        <a href="{{ route('admin.stores.edit', $store) }}" class="btn btn-warning btn-sm waves-effect">
                            <i class="material-icons">edit</i> Edit
                        </a>
                    </li>
                </ul>
            </div>
            <div class="body">
                <table class="table table-condensed">
                    <tr><th width="40%">Nama</th><td>{{ $store->name }}</td></tr>
                    <tr><th>Slug</th><td><code>{{ $store->slug }}</code></td></tr>
                    <tr><th>Tipe Bisnis</th><td>{{ ucfirst($store->business_type) }}</td></tr>
                    <tr><th>Pemilik</th><td>{{ $store->owner_name ?? '-' }}</td></tr>
                    <tr><th>Telepon</th><td>{{ $store->phone ?? '-' }}</td></tr>
                    <tr><th>Alamat</th><td>{{ $store->address ?? '-' }}</td></tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($store->is_active)
                                <span class="label label-success">Aktif</span>
                            @else
                                <span class="label label-danger">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr><th>Dibuat</th><td>{{ $store->created_at->format('d/m/Y H:i') }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Billing Aktif -->
    <div class="col-md-6">
        <div class="card">
            <div class="header">
                <h2>Billing Aktif</h2>
                <ul class="header-dropdown m-r--5">
                    <li>
                        <a href="{{ route('admin.stores.billing', $store) }}" class="btn btn-info btn-sm waves-effect">
                            <i class="material-icons">payment</i> Kelola Billing
                        </a>
                    </li>
                </ul>
            </div>
            <div class="body">
                @if($store->activeBilling)
                <table class="table table-condensed">
                    <tr><th width="40%">Status</th><td><span class="label label-success">{{ ucfirst($store->activeBilling->status) }}</span></td></tr>
                    <tr><th>Expired</th><td>{{ \Carbon\Carbon::parse($store->activeBilling->expired_at)->format('d/m/Y') }}</td></tr>
                    <tr><th>Tagihan</th><td>Rp {{ number_format($store->activeBilling->jumlah_tagihan, 0, ',', '.') }}</td></tr>
                    <tr><th>Bank</th><td>{{ $store->activeBilling->nama_bank ?? '-' }}</td></tr>
                    <tr><th>No. Rek</th><td>{{ $store->activeBilling->no_rek ?? '-' }}</td></tr>
                </table>
                @else
                <p class="text-muted">Tidak ada billing aktif.</p>
                <a href="{{ route('admin.stores.billing.create', $store) }}" class="btn btn-success btn-sm waves-effect">
                    <i class="material-icons">add</i> Tambah Billing
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <!-- Daftar Users -->
    <div class="col-md-7">
        <div class="card">
            <div class="header">
                <h2>Pengguna Toko</h2>
                <ul class="header-dropdown m-r--5">
                    <li>
                        <a href="{{ route('admin.stores.users.create', $store) }}" class="btn btn-success btn-sm waves-effect">
                            <i class="material-icons">person_add</i> Tambah User
                        </a>
                    </li>
                </ul>
            </div>
            <div class="body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Level</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($store->users as $user)
                        <tr>
                            <td>{{ $user->nama }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->level }}</td>
                            <td>{{ $user->role->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">Belum ada pengguna.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <a href="{{ route('admin.stores.users', $store) }}" class="btn btn-default btn-sm">Lihat Semua</a>
            </div>
        </div>
    </div>

    <!-- Link Konfigurasi -->
    <div class="col-md-5">
        <div class="card">
            <div class="header"><h2>Konfigurasi</h2></div>
            <div class="body">
                <a href="{{ route('admin.stores.business-config', $store) }}" class="btn btn-default btn-block waves-effect">
                    <i class="material-icons">settings</i> Konfigurasi Bisnis
                </a>
                <a href="{{ route('admin.stores.billing', $store) }}" class="btn btn-default btn-block waves-effect" style="margin-top:8px;">
                    <i class="material-icons">payment</i> Manajemen Billing
                </a>
                <a href="{{ route('admin.stores.users', $store) }}" class="btn btn-default btn-block waves-effect" style="margin-top:8px;">
                    <i class="material-icons">people</i> Manajemen Pengguna
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-xs-12">
        <a href="{{ route('admin.stores.index') }}" class="btn btn-default waves-effect">
            <i class="material-icons">arrow_back</i> Kembali
        </a>
    </div>
</div>
@endsection
