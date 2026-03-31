@extends('admin.layouts.app')

@section('title', 'Manajemen Toko')

@section('content')
<div class="block-header">
    <h2>Manajemen Toko</h2>
</div>

<div class="row clearfix">
    <div class="col-xs-12">
        <div class="card">
            <div class="header">
                <h2>Daftar Toko</h2>
                <ul class="header-dropdown m-r--5">
                    <li>
                        <a href="{{ route('admin.stores.create') }}" class="btn btn-success btn-sm waves-effect">
                            <i class="material-icons">add</i> Toko Baru
                        </a>
                    </li>
                </ul>
            </div>
            <div class="body table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Toko</th>
                            <th>Tipe Bisnis</th>
                            <th>Status Aktif</th>
                            <th>Status Billing</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stores as $store)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $store->name }}</td>
                            <td>
                                <span class="label label-default">{{ ucfirst($store->business_type) }}</span>
                            </td>
                            <td>
                                @if($store->is_active)
                                    <span class="label label-success">Aktif</span>
                                @else
                                    <span class="label label-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                @if($store->activeBilling)
                                    <span class="label label-success">{{ ucfirst($store->activeBilling->status) }}</span>
                                    <small class="text-muted">s/d {{ \Carbon\Carbon::parse($store->activeBilling->expired_at)->format('d/m/Y') }}</small>
                                @else
                                    <span class="label label-warning">Tidak Ada</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.stores.show', $store) }}" class="btn btn-info btn-xs waves-effect" title="Lihat">
                                    <i class="material-icons">visibility</i>
                                </a>
                                <a href="{{ route('admin.stores.edit', $store) }}" class="btn btn-warning btn-xs waves-effect" title="Edit">
                                    <i class="material-icons">edit</i>
                                </a>
                                <form action="{{ route('admin.stores.destroy', $store) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus toko {{ addslashes($store->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs waves-effect" title="Hapus">
                                        <i class="material-icons">delete</i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada toko.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="text-center">
                    {{ $stores->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
