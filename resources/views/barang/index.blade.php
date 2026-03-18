@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>DATA BARANG</h2>
                @if(auth()->user()->hasPermission('barang.create'))
                <a href="{{ route('barang.create') }}" class="btn btn-primary"><i class="material-icons">add</i></a>
                @endif
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-barang-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Barcode</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Lokasi</th>
                                <th>Rak</th>
                                <th>Harga Beli</th>
                                <th>Stok</th>
                                <th>Harga Jual</th>
                                <th>Profit</th>
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
    $('.js-barang-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('barang.data') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'kode_barcode', name: 'kode_barcode' },
            { data: 'nama_barang', name: 'nama_barang' },
            { data: 'satuan', name: 'satuan' },
            { data: 'nama_lokasi', name: 'nama_lokasi', searchable: false },
            { data: 'nama_rak', name: 'nama_rak', searchable: false },
            { data: 'harga_beli', name: 'harga_beli' },
            { data: 'stok', name: 'stok' },
            { data: 'harga_jual', name: 'harga_jual' },
            { data: 'profit', name: 'profit' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush
