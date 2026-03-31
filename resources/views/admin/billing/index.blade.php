@extends('admin.layouts.app')

@section('title', 'Billing Toko: ' . $store->name)

@section('content')
<div class="block-header">
    <h2>Billing Toko: {{ $store->name }}</h2>
</div>

<div class="row clearfix">
    <div class="col-xs-12">
        <div class="card">
            <div class="header">
                <h2>Daftar Billing</h2>
                <ul class="header-dropdown m-r--5">
                    <li>
                        <a href="{{ route('admin.stores.billing.create', $store) }}" class="btn btn-success btn-sm waves-effect">
                            <i class="material-icons">add</i> Tambah Billing
                        </a>
                    </li>
                </ul>
            </div>
            <div class="body table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Expired At</th>
                            <th>Jumlah Tagihan</th>
                            <th>Bank</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($billings as $billing)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($billing->expired_at)->format('d/m/Y') }}</td>
                            <td>Rp {{ number_format($billing->jumlah_tagihan, 0, ',', '.') }}</td>
                            <td>
                                {{ $billing->nama_bank ?? '-' }}
                                @if($billing->no_rek)
                                <small class="text-muted">({{ $billing->no_rek }})</small>
                                @endif
                            </td>
                            <td>
                                @if($billing->status === 'aktif')
                                    <span class="label label-success">Aktif</span>
                                @elseif($billing->status === 'expired')
                                    <span class="label label-danger">Expired</span>
                                @else
                                    <span class="label label-default">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                @if($billing->status !== 'aktif')
                                <form action="{{ route('admin.stores.billing.activate', [$store, $billing]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-xs waves-effect" title="Aktifkan">
                                        <i class="material-icons">check</i>
                                    </button>
                                </form>
                                @endif
                                @if($billing->status !== 'expired')
                                <form action="{{ route('admin.stores.billing.status', [$store, $billing, 'expired']) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning btn-xs waves-effect" title="Set Expired">
                                        <i class="material-icons">timer_off</i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada data billing.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
