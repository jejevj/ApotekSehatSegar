@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>DATA LOKASI & RAK BARANG</h2>
                @if(auth()->user()->hasPermission('rak.create'))
                <a href="{{ route('rak.create') }}" class="btn btn-primary"><i class="material-icons">add</i></a>
                @endif
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-rak-table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Lokasi</th>
                                <th>Rak</th>
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
    $('.js-rak-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('rak.data') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama_lokasi', name: 'nama_lokasi' },
            { data: 'nama_rak', name: 'nama_rak' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush
