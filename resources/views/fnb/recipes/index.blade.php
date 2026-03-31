@extends('layouts.app')

@section('content')
<div class="block-header">
    <h2>Resep FnB</h2>
</div>

<div class="row clearfix">
    <div class="col-xs-12">
        <div class="card">
            <div class="header">
                <h2>Daftar Resep</h2>
                @if(auth()->user()->hasPermission('recipes.create'))
                <div class="header-dropdown">
                    <a href="{{ route('fnb.recipes.create') }}" class="btn btn-primary waves-effect">
                        <i class="material-icons">add</i> Tambah Resep
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
                    <table id="recipesTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Nama Resep</th>
                                <th>Menu</th>
                                <th>Porsi</th>
                                <th>HPP/Porsi</th>
                                <th>Status HPP</th>
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
    $('#recipesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("fnb.recipes.data") }}',
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama_resep' },
            { data: 'menu_nama' },
            { data: 'porsi', searchable: false },
            { data: 'hpp_fmt', searchable: false },
            { data: 'hpp_status', orderable: false, searchable: false },
            { data: 'aksi', orderable: false, searchable: false, className: 'text-center' },
        ]
    });
});
</script>
@endpush
