@extends('layouts.app')

@section('content')
<div class="block-header">
    <h2>Bahan Baku FnB</h2>
</div>

<div class="row clearfix">
    <div class="col-xs-12">
        <div class="card">
            <div class="header">
                <h2>Daftar Bahan Baku</h2>
                @if(auth()->user()->hasPermission('ingredients.create'))
                <div class="header-dropdown">
                    <a href="{{ route('fnb.ingredients.create') }}" class="btn btn-primary waves-effect">
                        <i class="material-icons">add</i> Tambah Bahan Baku
                    </a>
                </div>
                @endif
            </div>
            <div class="body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="table-responsive">
                    <table id="ingredientsTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Nama Bahan</th>
                                <th>Satuan</th>
                                <th>Harga Beli</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('#ingredientsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("fnb.ingredients.data") }}',
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama' },
            { data: 'unit_nama' },
            { data: 'harga_beli_fmt', searchable: false },
            { data: 'stok_fmt', searchable: false },
            { data: 'stok_status', orderable: false, searchable: false },
            { data: 'aksi', orderable: false, searchable: false, className: 'text-center' },
        ]
    });
});
</script>
@endpush
