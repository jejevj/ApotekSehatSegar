@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>DATA PENJUALAN</h2>
                @if(auth()->user()->hasPermission('penjualan.create'))
                <a href="{{ route('penjualan.create') }}" class="btn btn-primary"><i class="material-icons">add</i> Tambah Transaksi</a>
                @endif
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-penjualan-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. Nota</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Kasir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    $('.js-penjualan-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("penjualan.data") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'kode_penjualan', name: 'kode_penjualan' },
            { data: 'tanggal', name: 'tanggal' },
            { data: 'pelanggan_nama', name: 'pelanggan_nama', searchable: false },
            { data: 'total_harga', name: 'total_harga' },
            { data: 'user_nama', name: 'user_nama', searchable: false },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush
