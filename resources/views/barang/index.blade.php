@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>{{ strtoupper('Data ' . label('product')) }}</h2>
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
                                <th>{{ 'Nama ' . label('product') }}</th>
                                <th>Kategori</th>
                                <th>Satuan</th>
                                @if($businessConfig->showProductLocation())
                                <th>{{ label('location') }}</th>
                                @endif
                                <th>Harga Beli</th>
                                <th>Stok</th>
                                <th>Harga Jual</th>
                                <th>Profit</th>
                                @if($businessConfig->get('business_type') === 'fnb')
                                <th>HPP / Margin</th>
                                @endif
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
            { data: 'nama_barang_formatted', name: 'nama_barang' },
            { data: 'nama_kategori', name: 'nama_kategori', searchable: false },
            { data: 'satuan', name: 'satuan' },
            @if($businessConfig->showProductLocation())
            { data: 'nama_lokasi_rak', name: 'nama_lokasi_rak', searchable: false },
            @endif
            { data: 'harga_beli', name: 'harga_beli' },
            { data: 'stok', name: 'stok' },
            { data: 'harga_jual', name: 'harga_jual' },
            { data: 'profit', name: 'profit' },
            @if($businessConfig->get('business_type') === 'fnb')
            { data: 'hpp_margin', name: 'hpp_margin', orderable: false, searchable: false },
            @endif
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush
