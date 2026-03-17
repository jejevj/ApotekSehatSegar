@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>DATA MENU</h2>
                @if(auth()->user()->hasPermission('menu.create'))
                <a href="{{ route('menu.create') }}" class="btn btn-primary"><i class="material-icons">add</i></a>
                @endif
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-menu-table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Menu</th>
                                <th>Icon</th>
                                <th>Route/URL</th>
                                <th>Parent</th>
                                <th>Urutan</th>
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
$(function() {
    $('.js-menu-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('menu.data') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'icon', name: 'icon', render: function(data) {
                return '<i class="material-icons">'+data+'</i>';
            }},
            { data: 'route_name', name: 'route_name', render: function(data, type, row) {
                return data ? data : row.url;
            }},
            { data: 'parent_name', name: 'parent_name' },
            { data: 'order', name: 'order' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush
